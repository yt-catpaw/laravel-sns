@extends('layouts.base')

@section('title', 'タイムライン')

@section('css')
    @vite('resources/css/pages/timeline.css')
@endsection

@section('content')
    <div class="timeline">
        @include('components.site-header')

        <div class="timeline__layout">
            <main class="timeline__feed">
                @if (session('status'))
                    <div class="alert alert--success">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert--error">
                        {{ $errors->first() }}
                    </div>
                @endif

                <section class="composer" aria-label="投稿フォーム">
                    <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="composer__body">
                            <div class="composer__avatar" aria-hidden="true">
                                {{ mb_substr(auth()->user()->name ?? 'U', 0, 1) }}
                            </div>
                            <div class="composer__main">
                                <textarea
                                    name="tweet"
                                    class="composer__input"
                                    placeholder="いまどうしてる？"
                                    aria-label="いまどうしてる？"
                                    required
                                >{{ old('tweet') }}</textarea>
                                <div class="composer__actions">
                                    <input type="file" name="image" accept="image/*" class="composer__file">
                                    <div class="composer__spacer" aria-hidden="true"></div>
                                    <button class="button button--primary" type="submit">投稿</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </section>

                <section class="timeline__posts" aria-label="投稿一覧">
                    @foreach ($posts ?? [] as $post)
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
                                @auth
                                    <button
                                        class="post-card__action js-like-btn"
                                        type="button"
                                        data-post-id="{{ $post->id }}"
                                        data-liked="{{ $post->is_liked ? '1' : '0' }}"
                                    >
                                        <span class="js-like-icon">{{ $post->is_liked ? '❤️' : '♡' }}</span>
                                        <span class="js-like-count">{{ $post->liked_users_count }}</span>
                                    </button>
                                @endauth

                                @guest
                                    <button class="post-card__action" type="button" disabled>
                                        ♡ {{ $post->liked_users_count }}
                                    </button>
                                @endguest

                                <a class="post-card__action" href="{{ route('posts.show', $post) }}">
                                    💬 <span>{{ $post->comments_count }}</span>
                                </a>
                                <span class="post-card__action">👁️ {{ $post->views_count ?? 0 }}</span>
                                <button class="post-card__action" type="button">↻</button>
                            </footer>
                        </article>
                    @endforeach
                </section>
            </main>

            <aside class="timeline__side">
                <div class="side-panel">
                    <h2 class="side-panel__title">おすすめ</h2>
                    <ul class="side-panel__list">
                        <li class="side-panel__item">カードやおすすめが入る想定</li>
                        <li class="side-panel__item">プレースホルダー項目</li>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
@endsection
