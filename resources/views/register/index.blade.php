@extends('layouts.base')

@section('title', '新規登録')

@section('css')
    @vite('resources/css/pages/register.css')
@endsection

@section('content')
<div class="register-page">
    <div class="register-page__visual">
        <div class="register-page__visual-inner">
            <h1 class="register-page__title">はじめよう、My SNS App</h1>
            <p class="register-page__subtitle">
                日々のつぶやきを気軽にシェア。新しいつながりを見つけましょう。
            </p>
        </div>
    </div>

    <div class="register-page__panel">
        <div class="register">
            <h2 class="register__title">新規登録</h2>
            <p class="register__lead">必要な情報を入力してアカウントを作成してください。</p>

            @if($errors->any())
                <div class="alert alert--error register__error">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('register.store') }}" class="form register__form">
                @csrf

                <div class="form__group">
                    <label class="form__label">ユーザー名</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="form__input">
                </div>

                <div class="form__group">
                    <label class="form__label">メールアドレス</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="form__input">
                </div>

                <div class="form__group">
                    <label class="form__label">パスワード</label>
                    <input type="password" name="password" required class="form__input">
                </div>

                <div class="form__group">
                    <label class="form__label">パスワード（確認）</label>
                    <input type="password" name="password_confirmation" required class="form__input">
                </div>

                <button type="submit" class="button button--primary form__submit">
                    アカウントを作成
                </button>
            </form>

            <div class="register__footer">
                <span>すでにアカウントをお持ちの方は</span>
                <a href="{{ route('login') }}" class="register__link">ログイン</a>
            </div>
        </div>
    </div>
</div>
@endsection
