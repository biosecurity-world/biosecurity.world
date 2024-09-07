@props(['title'])
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="length=device-length, user-scalable=yes, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }} | biosecurity.world</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:500,600,700" rel="stylesheet"/>

    @vite('resources/js/app.ts')
    @vite('resources/css/app.css')

    {{ $head ?? '' }}
</head>
<body {{ $attributes }}>
{{ $slot }}
</body>
</html>
