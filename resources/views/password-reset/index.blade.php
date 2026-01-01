@extends('layouts.base')

@section('title', 'パスワードリセット')

@section('css')
    @vite('resources/css/pages/password-reset.css')
@endsection

@section('content')
<div class="password-reset-page">
    <div class="password-reset-page__visual">
        <div class="password-reset-page__visual-inner">
            <h1 class="password-reset-page__title">パスワードをリセット</h1>
            <p class="password-reset-page__subtitle">
                登録メールアドレスを入力すると、リセット用のリンクをお送りします。
            </p>
        </div>
    </div>

    <div class="password-reset-page__panel">
        <div class="password-reset">
            <h2 class="password-reset__title">リセットリンクを送信</h2>
            <p class="password-reset__lead">登録したメールアドレスを入力してください。</p>

            @if(session('status'))
                <div class="alert alert--success password-reset__alert">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert--error password-reset__alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.reset.send') }}" class="form password-reset__form">
                @csrf

                <div class="form__group">
                    <label class="form__label">メールアドレス</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="form__input">
                </div>

                <button type="submit" class="button button--primary form__submit">
                    リセットリンクを送信
                </button>
            </form>

            <div class="password-reset__footer">
                <a href="{{ route('login') }}" class="password-reset__link">ログイン画面へ戻る</a>
            </div>
        </div>
    </div>
</div>
@endsection
