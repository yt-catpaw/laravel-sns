@extends('layouts.base')

@section('title', '決済確認')

@section('css')
    @vite('resources/css/pages/payment.css')
@endsection

@section('content')
    <div class="payment">
        @include('components.site-header')

        <div class="payment__layout">
            <section class="payment__hero" aria-label="決済概要">
                <div class="payment__hero-head">
                    <h1 class="payment__title">決済プレビュー</h1>
                    <p class="payment__desc">サーバーで受け取った内容を確認できます。</p>
                </div>
                <ol class="payment__steps" aria-label="ステップ">
                    <li class="is-complete">
                        <span class="payment__step-num">1</span>
                        <span class="payment__step-label">入力</span>
                    </li>
                    <li class="is-current" aria-current="step">
                        <span class="payment__step-num">2</span>
                        <span class="payment__step-label">確認</span>
                    </li>
                </ol>
            </section>

            <section class="payment__grid" aria-label="確認内容">
                <article class="panel">
                    <div class="panel__header">
                        <h2 class="panel__title">内容確認</h2>
                        <p class="panel__subtitle">この情報はサーバーで受け取った値を表示しています。</p>
                    </div>
                    <div class="confirm-summary">
                        <div class="confirm-summary__row">
                            <div class="confirm-summary__label">プラン</div>
                            <div class="confirm-summary__value">{{ $selectedPlan['title'] }}</div>
                        </div>
                        <div class="confirm-summary__row">
                            <div class="confirm-summary__label">金額</div>
                            <div class="confirm-summary__value">{{ $selectedPlan['price'] }}</div>
                        </div>
                        <div class="confirm-summary__row">
                            <div class="confirm-summary__label">カード名義</div>
                            <div class="confirm-summary__value">{{ $cardName }}</div>
                        </div>
                        <div class="confirm-summary__row">
                            <div class="confirm-summary__label">カード番号</div>
                            <div class="confirm-summary__value">{{ $cardLast4 }}</div>
                        </div>
                        <div class="confirm-summary__row">
                            <div class="confirm-summary__label">有効期限</div>
                            <div class="confirm-summary__value">{{ $exp }}</div>
                        </div>
                    </div>
                    <div class="confirm-actions">
                        <a class="button button--secondary" href="{{ route('payment.index') }}">戻る</a>
                        <a class="button button--primary" href="{{ route('payment.index') }}">この内容で完了（ダミー）</a>
                    </div>
                    <p class="confirm-note">ここまでの値はフロントで保持せず、サーバーへ送信されたものです。</p>
                </article>
            </section>
        </div>
    </div>
@endsection
