@extends('layouts.base')

@section('title', 'タイムライン（ダミー）')

@section('css')
    @vite('resources/css/pages/timeline.css')
@endsection

@section('content')
  <div class="timeline">
    <header class="timeline__header" role="banner">
        <div class="timeline__header-left">
            <a class="timeline__brand" href="/">
                <span class="timeline__logo">ロゴ</span>
            </a>
        </div>

        <div class="timeline__header-center">
            <form class="timeline__search" action="" method="GET">
                <input class="timeline__search-input" type="search" name="q" placeholder="検索" />
            </form>
        </div>

        <button class="timeline__menu-btn" type="button" aria-label="メニュー">☰</button>

        <div class="timeline__header-right">
            <nav class="timeline__nav" aria-label="メインナビゲーション">
                <a class="timeline__nav-link" href="#">ホーム</a>
                <a class="timeline__nav-link" href="#">通知</a>
                <a class="timeline__nav-link" href="#">自分</a>
            </nav>

            <form class="timeline__logout" action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="button button--outline timeline__logout-btn">ログアウト</button>
            </form>
        </div>
    </header>
  </div>
@endsection
