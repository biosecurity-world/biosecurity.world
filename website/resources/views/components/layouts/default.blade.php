@props(["title"])
<!doctype html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="length=device-length, user-scalable=yes, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    @vite('resources/js/app.ts')
    @vite('resources/css/app.css')
</head>
<body class="h-full antialiased w-full">
    {{ $slot }}

    <script src="https://unpkg.com/htmx.org@2.0.2"></script>
</body>
</html>
