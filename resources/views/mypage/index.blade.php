@extends('layouts.base')

@section('title', 'マイページ')

@section('css')
    @vite('resources/css/pages/mypage.css')
@endsection

@section('content')
    <div class="mypage">
        @include('components.site-header')

        <div class="mypage__layout">
            <section class="mypage__hero" aria-label="ユーザー概要">
                <div class="mypage__avatar" aria-hidden="true">
                    {{ mb_substr($user?->name ?? 'U', 0, 1) }}
                </div>
                <div class="mypage__meta">
                    <h1 class="mypage__title">{{ $user->name }}</h1>
                    <p class="mypage__description">{{ $user->email }}</p>
                    <div class="mypage__badges">
                        <span class="mypage__badge">登録日: {{ optional($user?->created_at)->format('Y/m/d') ?? '-' }}</span>
                        <span class="mypage__badge">ユーザーID: #{{ $user->id }}</span>
                    </div>
                    <div class="mypage__actions">
                        <a class="button button--primary mypage__action-btn" href="{{ route('analytics.index') }}">集計・分析へ</a>
                        <a class="button button--outline mypage__action-btn" href="#">課金処理へ</a>
                    </div>
                </div>
            </section>

            <section class="mypage__grid" aria-label="マイページの各機能">
                <article class="mypage-card">
                    <div class="mypage-card__heading">
                        <div class="mypage-card__icon" aria-hidden="true">📊</div>
                        <div>
                            <h2 class="mypage-card__title">集計・分析</h2>
                            <p class="mypage-card__subtitle">投稿の反響やトレンドの確認をここにまとめる予定です。</p>
                        </div>
                    </div>
                    <p class="mypage-card__text">
                        導線だけ先に置いています。実装ができたらリンク先を設定して、ダッシュボードとして活用してください。
                    </p>
                    <a class="mypage-card__link" href="{{ route('analytics.index') }}">分析ページへ</a>
                </article>

                <article class="mypage-card">
                    <div class="mypage-card__heading">
                        <div class="mypage-card__icon" aria-hidden="true">💳</div>
                        <div>
                            <h2 class="mypage-card__title">課金処理</h2>
                            <p class="mypage-card__subtitle">プラン変更や請求履歴などを扱う想定です。</p>
                        </div>
                    </div>
                    <p class="mypage-card__text">
                        課金フローは未実装です。必要になったらこのリンクを本番の決済画面に差し替えてください。
                    </p>
                    <a class="mypage-card__link" href="#">課金ページ（準備中）</a>
                </article>

                <article class="mypage-card mypage-card--secondary">
                    <div class="mypage-card__heading">
                        <div class="mypage-card__icon" aria-hidden="true">👤</div>
                        <div>
                            <h2 class="mypage-card__title">プロフィール</h2>
                            <p class="mypage-card__subtitle">ユーザー情報の確認用メモ</p>
                        </div>
                    </div>
                    <ul class="mypage-card__list">
                        <li>
                            <span class="mypage-card__label">表示名</span>
                            <span class="mypage-card__value">{{ $user->name }}</span>
                        </li>
                        <li>
                            <span class="mypage-card__label">メール</span>
                            <span class="mypage-card__value">{{ $user->email }}</span>
                        </li>
                        <li>
                            <span class="mypage-card__label">登録日</span>
                            <span class="mypage-card__value">{{ optional($user?->created_at)->format('Y/m/d') ?? '-' }}</span>
                        </li>
                    </ul>
                </article>
            </section>
        </div>
    </div>
@endsection
