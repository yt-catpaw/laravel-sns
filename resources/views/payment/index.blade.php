@extends('layouts.base')

@section('title', '決済ダミー画面')

@section('css')
    @vite('resources/css/pages/payment.css')
@endsection

@section('content')
    @php
        $defaultPlan = $plans[0] ?? [
            'type' => 'one_time',
            'title' => '単発購入',
            'price' => '¥3,000',
            'detail' => '1回きりの支払い。追加料金なし。',
        ];
    @endphp

    <div class="payment" data-payment-page data-stripe-key="{{ config('services.stripe.key') }}"
        data-intent-url="{{ route('payment.intent') }}" data-return-url="{{ route('payment.complete') }}">
        @include('components.site-header')

        <div class="payment__layout">
            <section class="payment__hero" aria-label="決済概要">
                <div class="payment__hero-head">
                    <h1 class="payment__title">決済入力</h1>
                    <p class="payment__desc">テスト決済の入力を行います。</p>
                </div>
                <ol class="payment__steps" aria-label="ステップ">
                    <li class="is-current" aria-current="step">
                        <span class="payment__step-num">1</span>
                        <span class="payment__step-label">入力</span>
                    </li>
                    <li>
                        <span class="payment__step-num">2</span>
                        <span class="payment__step-label">完了</span>
                    </li>
                </ol>
            </section>

            <section class="payment__grid" aria-label="決済フォーム">
                <article class="panel">
                    <div class="panel__header">
                        <h2 class="panel__title">プラン選択</h2>
                        <p class="panel__subtitle">単発 / サブスク を切り替えて体験できます。選択したプランで次のステップへ。</p>
                    </div>
                    <div class="plan-cards">
                        @foreach ($plans as $plan)
                            @php
                                $planTypeLabel = $plan['type'] === 'subscription' ? 'サブスク' : '単発';
                                $isActive = $loop->first;
                            @endphp
                            <div class="plan-card {{ $isActive ? 'is-active' : '' }}" tabindex="0" role="button"
                                aria-label="{{ $plan['title'] }}" aria-pressed="{{ $isActive ? 'true' : 'false' }}"
                                data-plan-card data-plan-type="{{ $plan['type'] }}"
                                data-plan-title="{{ $plan['title'] }}" data-plan-price="{{ $plan['price'] }}">
                                <div class="plan-card__badge {{ $plan['type'] === 'subscription' ? 'plan-card__badge--muted' : '' }}">
                                    {{ $planTypeLabel }}
                                </div>
                                <h3 class="plan-card__title">{{ $plan['title'] }}</h3>
                                <p class="plan-card__price">{{ $plan['price'] }}</p>
                                <p class="plan-card__desc">{{ $plan['detail'] }}</p>
                                <div class="plan-card__actions">
                                    <button class="button button--primary" type="button" data-plan-select>このプランを選ぶ</button>
                                    <span class="plan-card__hint">選択するだけ。決済は発生しません。</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </article>

                <article class="panel">
                    <div class="panel__header">
                        <h2 class="panel__title">支払い情報</h2>
                        <p class="panel__subtitle">入力はテスト用。実際の課金はありません。</p>
                    </div>
                    <form class="payment-form" method="GET" action="{{ route('payment.index') }}" data-payment-form>
                        <input type="hidden" name="plan_type" value="{{ $defaultPlan['type'] }}" data-plan-input>
                        <div class="payment-form__label">
                            <span>カード情報</span>
                            <div class="payment-element" id="payment-element" data-payment-element></div>
                        </div>
                        <p class="payment-form__note">テストカード: 4242 4242 4242 4242 / 任意の有効期限 / 任意のCVC</p>
                        <div class="payment-error" data-payment-error hidden role="alert">
                            <div class="error-box">
                                <div class="error-box__icon">!</div>
                                <div>
                                    <p class="error-box__title">決済エラー</p>
                                    <p class="error-box__body" data-payment-error-message>決済を完了できませんでした。</p>
                                </div>
                            </div>
                        </div>
                        <div class="payment-status" data-payment-status hidden aria-live="polite">
                            <p data-payment-status-message>決済の結果を確認しています。</p>
                        </div>
                        <div class="payment-summary">
                            <div>
                                <div class="payment-summary__label">選択中のプラン</div>
                                <div class="payment-summary__value">
                                    <span data-summary-plan>{{ $defaultPlan['title'] }}</span>
                                    <span class="payment-summary__price" data-summary-price>{{ $defaultPlan['price'] }}</span>
                                </div>
                            </div>
                            <div class="payment-summary__actions">
                                <button class="button button--primary" type="submit" data-payment-submit>支払いを確定する</button>
                            </div>
                        </div>
                    </form>
                </article>
            </section>
        </div>
    </div>
@endsection
