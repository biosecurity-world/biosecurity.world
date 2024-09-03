package main

import (
	"bufio"
	"encoding/json"
	"errors"
	"flag"
	"fmt"
	"net/url"
	"os"
	"path/filepath"
	"strings"
	"time"
)

var savePath string
var defaultLogoPath string
var useJSON bool

func run() error {
	scanner := bufio.NewScanner(os.Stdin)

	if _, err := os.Stat(savePath); err != nil {
		if !errors.Is(err, os.ErrNotExist) {
			return fmt.Errorf("error while checking the output directory: %w", err)
		}

		if err2 := os.Mkdir(savePath, os.FileMode(0744)); err2 != nil {
			return fmt.Errorf("error while creating output directory: %w", err2)
		}

		return err
	}

	if !useJSON {
		if savePath != "" {
			fmt.Printf("Output directory: %s\n", savePath)
		} else {
			fmt.Println("Output directory: current working directory")
		}

		fmt.Println("Listening to STDIN for CRLF-separated URLs...")
	}

	start := time.Now()
	processedCount := 0
	errorCount := 0

	if _, err := os.Stat(defaultLogoPath); err != nil {
		return fmt.Errorf("could not open default logo: %w", err)
	}

	defaultLogoFormat := filepath.Ext(defaultLogoPath)
	defaultLogoBody, err := os.ReadFile(defaultLogoPath)
	if err != nil {
		return err
	}

	for scanner.Scan() {
		URL := scanner.Text()

		if URL == "\r\n" {
			break
		}

		if len(URL) > 1<<16 {
			return errors.New("url must not be greater than 65,536 bytes")
		}

		parsedURL, err := url.ParseRequestURI(URL)
		if err != nil {
			return errors.New("url must be a valid url")
		}

		if !useJSON {
			fmt.Printf("- [TRY] %s\n", URL)
		}

		logo, err := FindLogo(parsedURL, 128)

		if err != nil {
			if !useJSON {
				fmt.Printf("- [ERR] %s (err: %s)\n", URL, err)
			}
			errorCount++

			logo = &Logo{
				Format: defaultLogoFormat[1:],
				Body:   defaultLogoBody,
			}
		}

		filename := parsedURL.Hostname()

		if logo.Filled {
			filename += ".filled"
		}

		filename += "." + logo.Format

		err = os.WriteFile(strings.TrimRight(savePath, "/")+"/"+filename, logo.Body, 0644)
		if err != nil {
			return err
		}

		if !useJSON {
			fmt.Printf("- [OK] %s -> %s\n", URL, filename)
		} else {
			marshalled, err := json.Marshal(&struct {
				Format string `json:"format"`
				Path   string `json:"path"`
				Filled bool   `json:"filled"`
				URL    string `json:"URL"`
			}{
				URL:    URL,
				Format: logo.Format,
				Path:   filename,
				Filled: logo.Filled,
			})
			if err != nil {
				return err
			}

			fmt.Println(string(marshalled))
		}
		processedCount++
	}

	if !useJSON {
		fmt.Printf("Processed %d logos with %d errors in %s\n", processedCount, errorCount, time.Since(start))
	}

	return nil
}

func main() {
	flag.StringVar(&savePath, "o", "", "directory where paths are written")
	flag.StringVar(&defaultLogoPath, "d", "", "default logo")
	flag.BoolVar(&useJSON, "json", false, "output useJSON")

	flag.Parse()

	err := run()
	if err != nil {
		_, _ = fmt.Fprintf(os.Stderr, "Error: %s\n", err)
		os.Exit(1)
	}
}
