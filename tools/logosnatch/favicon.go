package main

import (
	"bytes"
	"errors"
	"fmt"
	"github.com/kolesa-team/go-webp/encoder"
	"github.com/kolesa-team/go-webp/webp"
	_ "golang.org/x/image/bmp"
	_ "golang.org/x/image/webp"
	"golang.org/x/net/html"
	"image"
	_ "image/gif"
	_ "image/jpeg"
	_ "image/png"
	"io"
	_ "logosnatch/ico"
	"logosnatch/logopatch"
	"math"
	"net/http"
	"net/url"
	"sort"
	"strings"
	"time"
)

var (
	ErrUnreachableServer = errors.New("unreachable server")
	ErrLogoNotFound      = errors.New("logo not found")
)

type Logo struct {
	URL    string
	Format string
	Body   []byte
	Filled bool
	Size   int
}

func FindLogo(URL *url.URL, targetSize int) (*Logo, error) {
	res, err := doRequest("GET", URL.String())
	if err != nil {
		return nil, ErrUnreachableServer
	}

	z := html.NewTokenizer(res.Body)

	var base = ""
	var logos []*Logo

	for {
		tt := z.Next()
		if tt == html.ErrorToken { // includes EOF
			break
		}

		if tt != html.StartTagToken && tt != html.SelfClosingTagToken {
			continue
		}

		t := z.Token()

		if t.Data == "body" {
			break
		}

		if t.Data != "link" {
			continue
		}

		if t.Data == "base" && base == "" {
			for _, attr := range t.Attr {
				if attr.Key == "href" {
					base = attr.Val
				}
			}
			continue
		}

		relAttr := ""
		hrefAttr := ""

		for _, attr := range t.Attr {
			if attr.Key == "rel" {
				relAttr = attr.Val
				continue
			}

			if attr.Key == "href" {
				hrefAttr = attr.Val
				continue
			}
		}

		if (relAttr != "shortcut icon" && relAttr != "icon") || hrefAttr == "" {
			continue
		}

		logo, err := newLogo(hrefAttr, base, res.Request.URL.String())
		if err != nil {
			continue
		}

		logos = append(logos, logo)
	}

	if logo, err := newLogo("/favicon.ico", "", res.Request.URL.String()); err == nil {
		hasFavicon := false
		for _, logoCmp := range logos {
			if logo.URL == logoCmp.URL {
				hasFavicon = true
			}
		}

		if !hasFavicon {
			logos = append(logos, logo)
		}
	}

	if len(logos) == 0 {
		return nil, ErrLogoNotFound
	}

	sort.Slice(logos, func(i, j int) bool {
		diffI := math.Abs(float64(logos[i].Size - targetSize))
		diffJ := math.Abs(float64(logos[j].Size - targetSize))

		return diffI < diffJ
	})

	for _, logo := range logos {
		if logo.Format == "svg" {
			// TODO: We could do more here.
			logo.Filled = false
			return logo, nil
		}

		decodedLogo, _, err := image.Decode(bytes.NewReader(logo.Body))
		if err != nil {
			fmt.Println(err)
			continue
		}

		patchedLogo, filled := logopatch.Patch(decodedLogo)
		logo.Filled = filled

		options, err := encoder.NewLossyEncoderOptions(encoder.PresetIcon, 100)
		if err != nil {
			continue
		}

		buf := new(bytes.Buffer)
		if err = webp.Encode(buf, patchedLogo, options); err != nil {
			continue
		}

		logo.Format = "webp"
		logo.Body = buf.Bytes()

		return logo, nil
	}

	return nil, ErrLogoNotFound
}

func newLogo(href string, base string, pageURL string) (*Logo, error) {
	logoURL := getFullURLFromHref(href, base, pageURL)

	res, err := doRequest("GET", logoURL)
	if err != nil {
		return nil, err
	}

	byt, err := io.ReadAll(res.Body)
	if err != nil {
		res.Body.Close()
		return nil, err
	}

	res.Body.Close()

	conf, format, err := image.DecodeConfig(bytes.NewReader(byt))
	if err != nil {
		if errors.Is(err, image.ErrFormat) && !isSVG(byt) {
			return nil, err
		}

		conf = image.Config{Width: math.MaxInt, Height: math.MaxInt}
		format = "svg"
	}

	return &Logo{
		URL:    logoURL,
		Body:   byt,
		Format: format,
		Size:   max(conf.Width, conf.Height),
	}, nil
}

func getBaseURL(URL *url.URL) string {
	buf := &strings.Builder{}

	buf.WriteString(URL.Scheme)
	buf.WriteString("://")
	buf.WriteString(URL.Host)

	return buf.String()
}

func doRequest(method string, URL string) (*http.Response, error) {
	var response *http.Response
	var reqErr error
	retries := 3

	for retries > 0 {
		parsedURL, err := url.ParseRequestURI(URL)
		if err != nil {
			return nil, err
		}

		client := &http.Client{
			Timeout: 5 * time.Second,
		}

		req, err := http.NewRequest(method, URL, nil)
		if err != nil {
			return nil, err
		}

		baseURL := getBaseURL(parsedURL)

		// should bypass most WAFs
		req.Header.Add("User-Agent", "Mozilla/5.0 (X11; Linux x86_64; rv:124.0) Gecko/20100101 Firefox/124.0")
		req.Header.Add("DNT", "1")
		req.Header.Add("Accept", "image/avif,image/webp,*/*")
		req.Header.Add("Cache-Control", "no-cache")
		req.Header.Add("Referer", baseURL)
		req.Header.Add("Origin", baseURL)
		req.Header.Add("Sec-Fetch-Dest", "image")
		req.Header.Add("Sec-Fetch-Mode", "no-cors")
		req.Header.Add("Sec-Fetch-Site", "same-origin")

		response, reqErr = client.Do(req)
		if reqErr != nil {
			retries -= 1

			time.Sleep(time.Millisecond * 100)
		} else {
			break
		}
	}

	return response, reqErr
}

func getFullURLFromHref(href, base, pageURL string) string {
	URL := base + href

	if URL[0] == '/' {
		parsedLogoURL, _ := url.ParseRequestURI(pageURL)

		URL = getBaseURL(parsedLogoURL) + URL
	}

	if _, err := url.ParseRequestURI(URL); err != nil {
		URL = strings.TrimRight(pageURL, "/") + "/" + URL
	}

	return URL
}

func isSVG(byt []byte) bool {
	if len(byt) < 5 {
		return false
	}

	xmlOffset := 0
	// Starts with <?xml
	if byt[0] == 60 && byt[1] == 63 && byt[2] == 120 && byt[3] == 109 && byt[4] == 108 {
		xmlOffset = 5

		for xmlOffset < len(byt) {
			if byt[xmlOffset-1] == 63 && byt[xmlOffset] == 62 {
				xmlOffset++
				break
			}

			xmlOffset++
		}

	}

	z := html.NewTokenizer(bytes.NewReader(byt[xmlOffset:]))

	for {
		tt := z.Next()

		if tt == html.CommentToken {
			continue
		}

		if tt == html.TextToken {
			// If the text token isn't only whitespace, stop.
			if len(bytes.TrimSpace(z.Text())) != 0 {
				return false
			}
			continue
		}

		if tt != html.StartTagToken {
			return false
		}

		tagName, _ := z.TagName()
		return len(tagName) == 3 && tagName[0] == 115 && tagName[1] == 118 && tagName[2] == 103
	}
}
