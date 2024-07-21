@props(["title"])
<!doctype html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=yes, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>

    @vite('resources/js/app.ts')
    @vite('resources/css/app.css')
</head>
<body class="h-full antialiased w-full">
    {{ $slot }}
</body>
</html>
