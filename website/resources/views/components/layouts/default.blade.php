@props(['title'])
<!doctype html>
<html lang="en" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="user-scalable=yes, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }} | biosecurity.world</title>

    <link rel="preload" href="{{ Vite::asset('resources/fonts/Gilroy.woff2') }}" as="font" crossorigin/>
{{--    <link rel="preload" href="{{ Vite::asset('resources/fonts/Nunito-Regular.woff2') }}" as="font" crossorigin/>--}}
{{--    <link rel="preload" href="{{ Vite::asset('resources/fonts/Nunito-Bold.woff2') }}" as="font" crossorigin/>--}}

    {{-- todo: replace with a self-hosted, trimmed font file --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito:500,600,700,900" rel="stylesheet" />

    @vite('resources/css/main.css')

    {{ $head ?? '' }}
</head>
<body {{ $attributes }}>
{{ $slot }}
</body>
</html>
