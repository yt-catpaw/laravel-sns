@extends('layouts.base')

@section('title', '分析ダッシュボード（ダミー）')

@section('css')
    @vite('resources/css/pages/analytics.css')
@endsection

@section('content')
    <div class="analytics">
        @include('components.site-header')

        <div class="analytics__layout">
            <section class="analytics__hero" aria-label="分析ページ概要">
                <div class="analytics__hero-meta">
                    <h1 class="analytics__title">分析ダッシュボード（ダミー）</h1>
                    <p class="analytics__desc">
                        グラフを描くための器だけ用意したプレースホルダーです。データ取得や描画ロジックは未実装。
                    </p>
                    <div class="analytics__badges">
                        <span class="analytics__badge">ダミーデータ</span>
                        <span class="analytics__badge analytics__badge--muted">実装待ち</span>
                    </div>
                </div>
                <div class="analytics__filters" aria-label="期間フィルター">
                    <a
                        class="button button--secondary {{ ($range_key ?? '7d') === '7d' ? 'is-active' : '' }}"
                        href="{{ route('analytics.index', ['range' => '7d']) }}"
                    >過去7日</a>
                    <a
                        class="button button--secondary {{ ($range_key ?? '7d') === '30d' ? 'is-active' : '' }}"
                        href="{{ route('analytics.index', ['range' => '30d']) }}"
                    >過去30日</a>
                    <button class="button button--secondary" type="button" disabled>カスタム（後で）</button>
                </div>
            </section>

            <section class="metric-grid" aria-label="指標サマリー">
                <article class="metric-card">
                    <div class="metric-card__label">投稿数</div>
                    <div class="metric-card__value">{{ number_format($summary['posts_count'] ?? 0) }}</div>
                    <div class="metric-card__note">
                        期間: 過去{{ $range_days ?? 7 }}日 / 日次平均 {{ number_format($summary['posts_daily_avg'] ?? 0, 1) }}
                    </div>
                </article>
                <article class="metric-card">
                    <div class="metric-card__label">総いいね</div>
                    <div class="metric-card__value">{{ number_format($summary['likes_received'] ?? 0) }}</div>
                    <div class="metric-card__note">期間: 過去{{ $range_days ?? 7 }}日</div>
                </article>
                <article class="metric-card">
                    <div class="metric-card__label">総コメント</div>
                    <div class="metric-card__value">{{ number_format($summary['comments_received'] ?? 0) }}</div>
                    <div class="metric-card__note">期間: 過去{{ $range_days ?? 7 }}日</div>
                </article>
                <article class="metric-card">
                    <div class="metric-card__label">反応率</div>
                    <div class="metric-card__value">{{ $summary['reaction_rate'] ?? 0 }}%</div>
                    <div class="metric-card__note">（いいね＋コメント）/投稿数</div>
                </article>
            </section>

            <section class="analytics__grid" aria-label="グラフとリスト（ダミー）">
                <article class="panel panel--chart">
                    <div class="panel__header">
                        <div>
                            <h2 class="panel__title">推移グラフ</h2>
                            <p class="panel__subtitle">投稿数（棒）といいね数（折れ線）の想定。グラフライブラリで描画予定。</p>
                        </div>
                        <span class="panel__badge">placeholder</span>
                    </div>
                    <div class="chart-box">
                        <canvas id="chart-trend" aria-label="トレンドグラフ（未実装）"></canvas>
                        <div class="chart-box__overlay">グラフライブラリで描画予定 / 今はダミー</div>
                    </div>
                </article>

                <article class="panel">
                    <div class="panel__header">
                        <div>
                            <h2 class="panel__title">時間帯×曜日ヒートマップ</h2>
                            <p class="panel__subtitle">反応率を色で比較するマトリクスの器。データは未接続。</p>
                        </div>
                        <span class="panel__badge">placeholder</span>
                    </div>
                    <div class="heatmap" role="grid" aria-label="ヒートマップのダミー">
                        @foreach (['月','火','水','木','金','土','日'] as $day)
                            <div class="heatmap__row" role="row">
                                <div class="heatmap__label" role="gridcell">{{ $day }}</div>
                                <div class="heatmap__cell heatmap__cell--low" role="gridcell">朝</div>
                                <div class="heatmap__cell heatmap__cell--mid" role="gridcell">昼</div>
                                <div class="heatmap__cell heatmap__cell--high" role="gridcell">夜</div>
                            </div>
                        @endforeach
                    </div>
                </article>

                <article class="panel">
                    <div class="panel__header">
                        <div>
                            <h2 class="panel__title">トップ投稿ランキング</h2>
                            <p class="panel__subtitle">反応数でソートした想定。値はダミー。</p>
                        </div>
                        <span class="panel__badge">placeholder</span>
                    </div>
                    <ol class="top-posts">
                        <li>
                            <div class="top-posts__title">「新機能リリースしました」</div>
                            <div class="top-posts__meta">いいね 320 / コメント 48</div>
                        </li>
                        <li>
                            <div class="top-posts__title">「来週のアップデート予告」</div>
                            <div class="top-posts__meta">いいね 210 / コメント 32</div>
                        </li>
                        <li>
                            <div class="top-posts__title">「コミュニティイベント開催」</div>
                            <div class="top-posts__meta">いいね 160 / コメント 27</div>
                        </li>
                    </ol>
                </article>

                <article class="panel panel--secondary">
                    <div class="panel__header">
                        <div>
                            <h2 class="panel__title">ファネル（閲覧→反応）</h2>
                            <p class="panel__subtitle">脱落率を見る器。値はダミー。</p>
                        </div>
                        <span class="panel__badge">placeholder</span>
                    </div>
                    <ul class="funnel">
                        <li class="funnel__item" style="--progress: 100%">
                            <span class="funnel__label">閲覧</span>
                            <span class="funnel__bar"></span>
                            <span class="funnel__value">12,000</span>
                        </li>
                        <li class="funnel__item" style="--progress: 62%">
                            <span class="funnel__label">いいね</span>
                            <span class="funnel__bar"></span>
                            <span class="funnel__value">7,400</span>
                        </li>
                        <li class="funnel__item" style="--progress: 38%">
                            <span class="funnel__label">コメント</span>
                            <span class="funnel__bar"></span>
                            <span class="funnel__value">4,600</span>
                        </li>
                        <li class="funnel__item" style="--progress: 24%">
                            <span class="funnel__label">フォロー/投稿</span>
                            <span class="funnel__bar"></span>
                            <span class="funnel__value">2,900</span>
                        </li>
                    </ul>
                </article>
            </section>
        </div>
    </div>
@endsection
