@extends('layouts.base')

@section('title', '分析ダッシュボード')

@section('css')
    @vite('resources/css/pages/analytics.css')
@endsection

@section('content')
    <div class="analytics">
        @include('components.site-header')

        <div class="analytics__layout">
            <section class="analytics__hero" aria-label="分析ページ概要">
                <div class="analytics__hero-meta">
                    <h1 class="analytics__title">分析ダッシュボード</h1>
                    <p class="analytics__desc">
                        期間を指定して投稿の反応とファネルを確認できます。
                    </p>
                    <div class="analytics__controls" aria-label="期間フィルターとダウンロード">
                        <div class="analytics__filters">
                            <a
                                class="button button--secondary {{ ($range_key ?? '7d') === '7d' ? 'is-active' : '' }}"
                                href="{{ route('analytics.index', ['range' => '7d']) }}"
                            >過去7日</a>
                            <a
                                class="button button--secondary {{ ($range_key ?? '7d') === '30d' ? 'is-active' : '' }}"
                                href="{{ route('analytics.index', ['range' => '30d']) }}"
                            >過去30日</a>
                            <form class="analytics__custom-range" action="{{ route('analytics.index') }}" method="GET">
                                <input type="hidden" name="range" value="custom">
                                <span class="analytics__custom-range-label">日付指定</span>
                                <label class="sr-only" for="from">開始日</label>
                                <input
                                    id="from"
                                    name="from"
                                    type="date"
                                    value="{{ $range_from ?? '' }}"
                                    aria-label="開始日"
                                >
                                <span class="analytics__custom-range-sep">〜</span>
                                <label class="sr-only" for="to">終了日</label>
                                <input
                                    id="to"
                                    name="to"
                                    type="date"
                                    value="{{ $range_to ?? '' }}"
                                    aria-label="終了日"
                                >
                                <button
                                    class="button button--secondary {{ ($range_key ?? '7d') === 'custom' ? 'is-active' : '' }}"
                                    type="submit"
                                >適用</button>
                            </form>
                        </div>
                        <div class="analytics__actions">
                            <a
                                class="button button--primary"
                                href="{{ route('exports.daily_summaries', ['from' => $range_from ?? null, 'to' => $range_to ?? null]) }}"
                            >
                                <span class="analytics__actions-line">CSVダウンロード</span>
                                <span class="analytics__actions-sub">日別の投稿数・いいね・コメント</span>
                            </a>
                            <p class="analytics__actions-note">
                                ※ 選択中の期間（過去7日/30日/日付指定）がそのままCSVに反映されます。
                            </p>
                        </div>
                    </div>
                </div>
        </section>

            <section class="metric-grid" aria-label="指標サマリー">
                <article class="metric-card">
                    <div class="metric-card__label">投稿数</div>
                    <div class="metric-card__value">{{ number_format($summary['posts_count'] ?? 0) }}</div>
                    <div class="metric-card__note">
                        期間: {{ $range_from ?? '' }} 〜 {{ $range_to ?? '' }} / 日次平均 {{ number_format($summary['posts_daily_avg'] ?? 0, 1) }}
                    </div>
                </article>
                <article class="metric-card">
                    <div class="metric-card__label">総いいね</div>
                    <div class="metric-card__value">{{ number_format($summary['likes_received'] ?? 0) }}</div>
                    <div class="metric-card__note">期間: {{ $range_from ?? '' }} 〜 {{ $range_to ?? '' }}</div>
                </article>
                <article class="metric-card">
                    <div class="metric-card__label">総コメント</div>
                    <div class="metric-card__value">{{ number_format($summary['comments_received'] ?? 0) }}</div>
                    <div class="metric-card__note">期間: {{ $range_from ?? '' }} 〜 {{ $range_to ?? '' }}</div>
                </article>
                <article class="metric-card">
                    <div class="metric-card__label">反応率</div>
                    <div class="metric-card__value">{{ $summary['reaction_rate'] ?? 0 }}%</div>
                    <div class="metric-card__note">（いいね＋コメント）/投稿数</div>
                </article>
            </section>

            <section class="analytics__grid" aria-label="グラフとリスト">
                <article class="panel panel--chart">
                    <div class="panel__header">
                        <div>
                            <h2 class="panel__title">推移グラフ</h2>
                            <p class="panel__subtitle">投稿数（棒）といいね数（折れ線）</p>
                        </div>
                    </div>
                    <div class="chart-box chart-box--trend">
                        <div class="chart-box__canvas" data-analytics-trend aria-label="トレンドグラフ"></div>
                    </div>
                    <script type="application/json" id="trend-data">
                        {!! json_encode($trend_data ?? [], JSON_UNESCAPED_UNICODE) !!}
                    </script>
                </article>

                <article class="panel">
                    <div class="panel__header">
                        <div>
                            <h2 class="panel__title">時間帯×曜日ヒートマップ</h2>
                            <p class="panel__subtitle">反応数を色で比較します。</p>
                        </div>
                    </div>
                    <div class="chart-box chart-box--heatmap">
                        <div class="chart-box__canvas" data-analytics-heatmap aria-label="ヒートマップ"></div>
                    </div>
                    <script type="application/json" id="heatmap-data">
                        {!! json_encode($heatmap_data ?? [], JSON_UNESCAPED_UNICODE) !!}
                    </script>
                    <p class="chart-note">
                        ※ユーザーの投稿に付いた「いいね＋コメント」の件数を曜日×時間帯で集計しています。
                    </p>
                    <p class="chart-note">
                        ※時間帯は「0-5 / 6-11 / 12-17 / 18-23」の4区分、値は件数そのまま（投稿数で割った正規化なし）です。
                    </p>
                </article>

                <article class="panel">
                    <div class="panel__header">
                        <div>
                            <h2 class="panel__title">トップ投稿</h2>
                            <p class="panel__subtitle">反応が多い順に並べています。</p>
                        </div>
                        <span class="panel__badge">期間: {{ $range_from ?? '' }} 〜 {{ $range_to ?? '' }}</span>
                    </div>
                    @if (!empty($top_posts))
                        <ol class="top-posts">
                            @foreach ($top_posts as $index => $post)
                                <li class="top-posts__item">
                                    <div class="top-posts__rank top-posts__rank--{{ $index + 1 }}">{{ $index + 1 }}</div>
                                    <div class="top-posts__title">{{ $post['title'] }}</div>
                                    <div class="top-posts__metrics">
                                        <span class="badge badge--like">♡ {{ number_format($post['likes']) }}</span>
                                        <span class="badge badge--comment">💬 {{ number_format($post['comments']) }}</span>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    @else
                        <div class="top-posts__empty">対象期間の投稿がありません。</div>
                    @endif
                </article>

                <article class="panel panel--secondary">
                    <div class="panel__header">
                        <div>
                            <h2 class="panel__title">ファネル（閲覧→反応）</h2>
                            <p class="panel__subtitle">閲覧からいいね・コメントへの遷移状況を可視化します。</p>
                        </div>
                    </div>
                    <div class="chart-box chart-box--funnel">
                        <div class="chart-box__canvas" data-analytics-funnel aria-label="ファネルグラフ"></div>
                    </div>
                    <script type="application/json" id="funnel-data">
                        {!! json_encode($funnel_data ?? [], JSON_UNESCAPED_UNICODE) !!}
                    </script>
                    <p class="chart-note">
                        ※期間内の閲覧件数を基準に、いいね・コメント・反応合計の比率を表示しています。
                    </p>
                </article>
            </section>
        </div>
    </div>
@endsection
