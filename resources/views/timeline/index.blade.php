@extends('layouts.base')

@section('title', 'タイムライン（ダミー）')

@section('css')
    @vite('resources/css/pages/timeline.css')
@endsection

@section('content')
  <div class="timeline">
    @include('components.site-header')

    <div class="timeline__layout">
        <main class="timeline__feed">
            <section class="composer" aria-label="投稿フォーム">
                <div class="composer__body">
                    <div class="composer__avatar" aria-hidden="true">A</div>
                    <div class="composer__main">
                        <textarea class="composer__input" placeholder="いまどうしてる？" aria-label="いまどうしてる？"></textarea>
                        <div class="composer__actions">
                            <button class="button button--secondary composer__action" type="button">画像</button>
                            <div class="composer__spacer" aria-hidden="true"></div>
                            <button class="button button--primary" type="button">投稿</button>
                        </div>
                    </div>
                </div>
            </section>

            <section class="timeline__posts" aria-label="投稿一覧">
                <article class="post-card">
                    <header class="post-card__header">
                        <div class="post-card__avatar" aria-hidden="true">U</div>
                        <div class="post-card__meta">
                            <div class="post-card__name">山田 太郎</div>
                            <div class="post-card__id">@yamada · 2分前</div>
                        </div>
                        <button class="post-card__menu" type="button" aria-label="メニュー">…</button>
                    </header>
                    <div class="post-card__content">
                        <p>本文がここに入ります。ダミーテキスト。</p>
                        <figure class="post-card__media">[画像が入る想定]</figure>
                    </div>
                    <footer class="post-card__footer">
                        <button class="post-card__action" type="button">♡ 12</button>
                        <button class="post-card__action" type="button">💬 3</button>
                        <button class="post-card__action" type="button">↻ 1</button>
                    </footer>
                </article>

                <article class="post-card post-card--placeholder">
                    <header class="post-card__header">
                        <div class="post-card__avatar" aria-hidden="true">U</div>
                        <div class="post-card__meta">
                            <div class="post-card__name">名前</div>
                            <div class="post-card__id">@id · 時間</div>
                        </div>
                        <button class="post-card__menu" type="button" aria-label="メニュー">…</button>
                    </header>
                    <div class="post-card__content">
                        <p>本文がここに入ります。</p>
                    </div>
                    <footer class="post-card__footer">
                        <button class="post-card__action" type="button">♡</button>
                        <button class="post-card__action" type="button">💬</button>
                        <button class="post-card__action" type="button">↻</button>
                    </footer>
                </article>
            </section>
        </main>

        <aside class="timeline__side">
            <div class="side-panel">
                <h2 class="side-panel__title">おすすめ</h2>
                <ul class="side-panel__list">
                    <li class="side-panel__item">カードやおすすめが入る想定</li>
                    <li class="side-panel__item">ダミー項目</li>
                </ul>
            </div>
        </aside>
        </div>
  </div>
@endsection
