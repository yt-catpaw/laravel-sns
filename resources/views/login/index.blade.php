@extends('layouts.base')

@section('title', 'ログイン')

@section('css')
    @vite('resources/css/pages/login.css')
@endsection

@section('content')
<div class="login-wrapper">
    <div class="login">
        <h1 class="login__title">ログイン</h1>

        @if($errors->any())
            <div class="alert alert--error login__error">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="/login" class="form login__form">
            @csrf

            <div  class="form__group">
                <label class="form__label">メールアドレス</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="form__input">
            </div>

            <div  class="form__group">
            <div>
                <label class="form__label">パスワード</label>
                <input type="password" name="password" required class="form__input">
            </div>

            <button type="submit" class="button button--primary form__submit">
                ログイン
            </button>
        </form>
    </div>
</div>
@endsection