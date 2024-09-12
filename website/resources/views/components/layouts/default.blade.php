@props(['title'])
<!doctype html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="user-scalable=yes, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }} | biosecurity.world</title>

    <link rel="preload" href="{{ Vite::asset('resources/fonts/Gilroy.woff2') }}" as="font" crossorigin/>
    <link rel="preload" href="{{ Vite::asset('resources/fonts/Nunito-Regular.woff2') }}" as="font" crossorigin/>
    <link rel="preload" href="{{ Vite::asset('resources/fonts/Nunito-Bold.woff2') }}" as="font" crossorigin/>

    @vite('resources/css/main.css')

    {{ $head ?? '' }}
</head>
<body {{ $attributes }}>
{{ $slot }}
</body>
</html>
