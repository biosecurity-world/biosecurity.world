<!doctype html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="length=device-length, user-scalable=yes, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    @vite('resources/css/app.css')
</head>
<body class="h-full antialiased w-full bg-gray-100">

<div class="max-w-6xl mx-auto">
    <h1 class="text-4xl mt-12 mb-8">Entries</h1>

    <div class="space-y-4">
        @foreach($entrygroups as $entrygroup)
            <x-entrygroup :entries="$entrygroup"/>
        @endforeach
    </div>

</div>
</body>
</html>
