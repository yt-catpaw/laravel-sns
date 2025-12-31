@extends('layouts.base')

@section('title', '投稿詳細')

@section('css')
    @vite('resources/css/pages/timeline.css')
@endsection

@section('content')
    <div class="timeline">
        @include('components.site-header')

        <div class="timeline__layout">
            <main class="timeline__feed">

                <div class="post-show__back" style="margin-bottom: 12px;">
                    <a href="{{ route('timeline.index') }}">← タイムラインに戻る</a>
                </div>

                <section class="timeline__posts" aria-label="投稿詳細">
                    <article class="post-card">
                        <header class="post-card__header">
                            <div class="post-card__avatar" aria-hidden="true">
                                {{ mb_substr($post->user->name ?? 'U', 0, 1) }}
                            </div>
                            <div class="post-card__meta">
                                <div class="post-card__name">{{ $post->user->name ?? '名無し' }}</div>
                                <div class="post-card__id">
                                    {{ $post->created_at?->diffForHumans() ?? '' }}
                                </div>
                            </div>
                        </header>

                        <div class="post-card__content">
                            <p>{{ $post->tweet }}</p>

                            @if ($post->image_path)
                                <figure class="post-card__media">
                                    <img src="{{ asset($post->image_path) }}" alt="投稿画像" class="post-card__image">
                                </figure>
                            @endif
                        </div>

                        <footer class="post-card__footer">
                            {{-- ここはタイムラインと同じUIにしたいなら後で足す --}}
                            <span class="post-card__action">❤️ {{ $post->liked_users_count ?? 0 }}</span>
                            <span class="post-card__action">💬 {{ $post->comments_count ?? 0 }}</span>
                        </footer>
                    </article>
                </section>

                <section class="comments" aria-label="返信一覧" style="margin-top: 16px;">
                <h2 class="comments__title" style="font-size: 14px; margin-bottom: 8px;">
                    返信（{{ $post->comments_count ?? $post->comments->count() }}）
                </h2>

                    @forelse ($post->comments as $comment)
                        <article class="post-card" style="margin-top: 10px;">
                            <header class="post-card__header">
                                <div class="post-card__avatar" aria-hidden="true">
                                    {{ mb_substr($comment->user->name ?? 'U', 0, 1) }}
                                </div>
                                <div class="post-card__meta">
                                    <div class="post-card__name">{{ $comment->user->name ?? '名無し' }}</div>
                                    <div class="post-card__id">
                                        {{ $comment->created_at?->diffForHumans() ?? '' }}
                                    </div>
                                </div>
                            </header>

                            <div class="post-card__content">
                                <p>{{ $comment->body }}</p>
                            </div>
                        </article>
                    @empty
                        <p style="color: #666; font-size: 14px;">
                            まだ返信はありません
                        </p>
                    @endforelse
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
