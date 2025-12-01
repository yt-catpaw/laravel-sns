<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'アプリ名')</title>

    @vite('resources/css/app.css')
</head>
<body>
    <main>
        @yield('content')
    </main>
</body>
</html>
