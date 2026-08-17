@props ([
    "title",
    "description" => null,
    "canonical" => null,
    "image" => null,
    "jsonLd" => null,
])
@php
    $seoTitle = $title . " | " . config("seo.site_name");
    $seoDescription = \Illuminate\Support\Str::limit(
        trim(preg_replace('/\s+/', " ", $description ?? config("seo.description")) ?? ""),
        160,
    );
    // Cloudflare Pages serves the exported directories with a trailing slash and
    // 308s the slashless form, so the canonical must carry it or it would point
    // at a redirect.
    $seoUrl = $canonical === null ? null : config("seo.url") . rtrim($canonical, "/") . "/";
@endphp
<!DOCTYPE html>
<html lang="en" class="">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="user-scalable=yes, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}" />

    @if ($seoUrl)
        <link rel="canonical" href="{{ $seoUrl }}" />
    @endif

    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="{{ config("seo.site_name") }}" />
    <meta property="og:title" content="{{ $seoTitle }}" />
    <meta property="og:description" content="{{ $seoDescription }}" />
    @if ($seoUrl)
        <meta property="og:url" content="{{ $seoUrl }}" />
    @endif
    @if ($image)
        <meta property="og:image" content="{{ $image }}" />
    @endif

    <meta name="twitter:card" content="{{ $image ? "summary" : "summary_large_image" }}" />
    <meta name="twitter:title" content="{{ $seoTitle }}" />
    <meta name="twitter:description" content="{{ $seoDescription }}" />
    @if ($image)
        <meta name="twitter:image" content="{{ $image }}" />
    @endif

    @if ($jsonLd)
        {{-- HEX flags keep a stray "</script>" or quote in Notion copy from breaking out of the tag. --}}
        <script type="application/ld+json">
            {!! json_encode(
                $jsonLd,
                JSON_UNESCAPED_SLASHES |
                    JSON_UNESCAPED_UNICODE |
                    JSON_HEX_TAG |
                    JSON_HEX_AMP |
                    JSON_HEX_APOS |
                    JSON_HEX_QUOT,
            ) !!}
        </script>
    @endif

    <link rel="preload" href="/fonts/space-grotesk-latin.woff2" as="font" type="font/woff2" crossorigin />

    @vite ("resources/css/main.css")

    {!! $head ?? "" !!}
</head>
<body {{ $attributes }}>
    {{ $slot }}
</body>
</html>
