@extends('layouts.base')

@section('title', '決済完了')

@section('css')
    @vite('resources/css/pages/payment.css')
@endsection

@section('content')
    <div class="payment">
        @include('components.site-header')

        <div class="payment__layout">
            <section class="payment__hero" aria-label="決済完了">
                <div class="payment__hero-head">
                    <h1 class="payment__title">決済完了</h1>
                    <p class="payment__desc">決済ステータスを確認しました。</p>
                </div>
            </section>

            <section class="payment__grid" aria-label="決済結果">
                <article class="panel">
                    <div class="panel__header">
                        <h2 class="panel__title">結果</h2>
                        <p class="panel__subtitle">{{ $message }}</p>
                    </div>
                    <div class="confirm-actions">
                        <a class="button button--secondary" href="{{ route('payment.index') }}">決済画面へ戻る</a>
                        <a class="button button--primary" href="{{ route('mypage.show') }}">マイページへ</a>
                    </div>
                </article>
            </section>
        </div>
    </div>
@endsection
