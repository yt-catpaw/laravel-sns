<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン</title>

    @vite('resources/css/app.css')  
</head>
<body>
    <h1>ログイン</h1>

    @if($errors->any())
        <div style="color:red;">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="/login">
        @csrf

        <div>
            <label>メールアドレス</label>
            <input type="email" name="email" value="{{ old('email') }}" required>
        </div>

        <div>
            <label>パスワード</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit">ログイン</button>
    </form>
</body>
</html>
