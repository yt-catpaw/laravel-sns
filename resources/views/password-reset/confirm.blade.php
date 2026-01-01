@extends('layouts.base')

@section('title', 'パスワード再設定')

@section('css')
    @vite('resources/css/pages/password-reset-confirm.css')
@endsection

@section('content')
<div class="reset-confirm-page">
    <div class="reset-confirm-page__visual">
        <div class="reset-confirm-page__visual-inner">
            <p class="reset-confirm-page__eyebrow">Password Reset</p>
            <h1 class="reset-confirm-page__title">新しいパスワードを設定</h1>
            <p class="reset-confirm-page__subtitle">
                メールで届いたリンクからアクセスしています。安全なパスワードで、アカウントを守りましょう。
            </p>
            <ul class="reset-confirm-page__tips">
                <li>リンクの有効期限は30分です</li>
                <li>数字・記号を混ぜて8文字以上がおすすめです</li>
            </ul>
        </div>
    </div>

    <div class="reset-confirm-page__panel">
        <div class="reset-confirm">
            <div class="reset-confirm__head">
                <div class="reset-confirm__steps">
                    <span class="chip chip--muted">1. リンク送信</span>
                    <span class="chip chip--active">2. パスワード変更</span>
                </div>
                <h2 class="reset-confirm__title">あと少しで完了です</h2>
                <p class="reset-confirm__lead">新しいパスワードを設定してください。</p>
            </div>

            @if(session('status'))
                <div class="alert alert--success reset-confirm__alert">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert--error reset-confirm__alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.reset.update') }}" class="form reset-confirm__form">
                @csrf

                <input type="hidden" name="email" value="{{ old('email', $email) }}">
                <input type="hidden" name="token" value="{{ old('token', $token) }}">

                <div class="form__group">
                    <label for="password" class="form__label">新しいパスワード</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="form__input"
                        required
                        autocomplete="new-password"
                    >
                </div>

                <div class="form__group">
                    <label for="password_confirmation" class="form__label">新しいパスワード（確認）</label>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        class="form__input"
                        required
                        autocomplete="new-password"
                    >
                </div>

                <button type="submit" class="button button--primary form__submit">
                    パスワードを更新する
                </button>
            </form>

            <p class="reset-confirm__helper">
                パスワードの使い回しを避け、定期的な変更がおすすめです。
            </p>

            <div class="reset-confirm__footer">
                <a href="{{ route('login') }}" class="reset-confirm__link">ログイン画面へ戻る</a>
            </div>
        </div>
    </div>
</div>
@endsection
