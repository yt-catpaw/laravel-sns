@extends('layouts.base')

@section('title', 'ログイン')

@section('css')
    @vite('resources/css/pages/login.css')
@endsection

@section('content')
<div class="login-page">
    <div class="login-page__visual">
        <div class="login-page__visual-inner">
            <h1 class="login-page__title">My SNS App</h1>
            <p class="login-page__subtitle">
                みんなの日常が流れる、シンプルなSNS。
            </p>
            {{-- 画像入れたいならここに img --}}
            {{-- <img src="/images/login-visual.png" alt="" class="login-page__image"> --}}
        </div>
    </div>

    <div class="login-page__panel">
        <div class="login">
            <h2 class="login__title">ログイン</h2>

            @if($errors->any())
                <div class="alert alert--error login__error">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="form login__form">
                @csrf

                <div class="form__group">
                    <label class="form__label">メールアドレス</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="form__input">
                </div>

                <div class="form__group">
                    <label class="form__label">パスワード</label>
                    <input type="password" name="password" required class="form__input">
                </div>

                <button type="submit" class="button button--primary form__submit">
                    ログイン
                </button>
            </form>

            <div class="login__footer">
                <div class="login__footer-row">
                    <span>アカウントをお持ちでない方は</span>
                    <a href="{{ route('register.show') }}" class="login__link">新規登録</a>
                </div>
                <div class="login__footer-row">
                    <a href="{{ route('password.reset.show') }}" class="login__link">パスワードをお忘れですか？</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
