<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'アプリ名')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('css')
</head>
<body>
    <main>
        @yield('content')
    </main>
</body>
</html>
