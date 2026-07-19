@props([
    "title",
])
<!DOCTYPE html>
<html lang="en" class="">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="user-scalable=yes, initial-scale=1.0" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge" />
        <title>{{ $title }} | biosecurity.world</title>

        <link rel="preload" href="/fonts/space-grotesk-latin.woff2" as="font" type="font/woff2" crossorigin />

        @vite("resources/css/main.css")

        {!! $head ?? "" !!}
    </head>
    <body {{ $attributes }}>
        {{ $slot }}
    </body>
</html>
