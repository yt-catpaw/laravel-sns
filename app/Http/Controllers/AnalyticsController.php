<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostView;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AnalyticsController extends Controller
{
    public function show(Request $request)
    {
        $user = auth()->user();

        $range = $this->normalizeRange($request);

        $summary = $this->buildSummaryForUser(
            $user,
            $range['from'],
            $range['to'],
            $range['days']
        );

        $topPosts = $this->topPostsForUser(
            $user,
            $range['from'],
            $range['to']
        );

        $trendData = $this->buildTrendData(
            $user,
            $range['from'],
            $range['to']
        );
        $heatmapData = $this->buildHeatmapData(
            $user,
            $range['from'],
            $range['to']
        );
        $funnelData = $this->buildFunnelData(
            $user,
            $range['from'],
            $range['to']
        );

        return view('analytics.index', [
            'summary' => $summary,
            'range_key' => $range['key'],
            'range_days' => $range['days'],
            'range_from' => $range['from']->toDateString(),
            'range_to' => $range['to']->toDateString(),
            'top_posts' => $topPosts,
            'trend_data' => $trendData,
            'heatmap_data' => $heatmapData,
            'funnel_data' => $funnelData,
        ]);
    }

    private function buildSummaryForUser(User $user, Carbon $from, Carbon $to, int $rangeDays): array
    {
        $posts = Post::where('user_id', $user->id)
            ->whereBetween('created_at', [$from, $to])
            ->withCount(['likedUsers', 'comments'])
            ->get();

        $postsCount = $posts->count();
        $likesReceived = $posts->sum('liked_users_count');
        $commentsReceived = $posts->sum('comments_count');
        $reactionRate = $postsCount > 0
            ? round((($likesReceived + $commentsReceived) / $postsCount) * 100, 1)
            : 0;
        $postsDailyAverage = $rangeDays > 0
            ? round($postsCount / $rangeDays, 1)
            : $postsCount;

        return [
            'posts_count' => $postsCount,
            'likes_received' => $likesReceived,
            'comments_received' => $commentsReceived,
            'reaction_rate' => $reactionRate,
            'posts_daily_avg' => $postsDailyAverage,
        ];
    }

    private function buildTrendData(User $user, Carbon $from, Carbon $to): array
    {
        $labels = [];
        $cursor = $from->copy()->startOfDay();
        while ($cursor->lte($to)) {
            $labels[] = $cursor->toDateString();
            $cursor->addDay();
        }

        $postsByDate = Post::where('user_id', $user->id)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->groupBy('d')
            ->pluck('c', 'd');

        $likesByDate = Post::where('posts.user_id', $user->id)
            ->leftJoin('likes', 'posts.id', '=', 'likes.post_id')
            ->whereBetween('likes.created_at', [$from, $to])
            ->selectRaw('DATE(likes.created_at) as d, COUNT(*) as c')
            ->groupBy('d')
            ->pluck('c', 'd');

        $postsSeries = [];
        $likesSeries = [];
        foreach ($labels as $date) {
            $postsSeries[] = (int) ($postsByDate[$date] ?? 0);
            $likesSeries[] = (int) ($likesByDate[$date] ?? 0);
        }

        return [
            'labels' => $labels,
            'posts' => $postsSeries,
            'likes' => $likesSeries,
        ];
    }

    private function buildHeatmapData(User $user, Carbon $from, Carbon $to): array
    {
        $days = ['月', '火', '水', '木', '金', '土', '日'];
        $slots = [
            ['label' => '0-5', 'start' => 0, 'end' => 5],
            ['label' => '6-11', 'start' => 6, 'end' => 11],
            ['label' => '12-17', 'start' => 12, 'end' => 17],
            ['label' => '18-23', 'start' => 18, 'end' => 23],
        ];

        $matrix = array_fill(0, 7, array_fill(0, count($slots), 0));

        $likeTimes = DB::table('likes')
            ->join('posts', 'likes.post_id', '=', 'posts.id')
            ->where('posts.user_id', $user->id)
            ->whereBetween('likes.created_at', [$from, $to])
            ->pluck('likes.created_at');

        $commentTimes = DB::table('comments')
            ->join('posts', 'comments.post_id', '=', 'posts.id')
            ->where('posts.user_id', $user->id)
            ->whereBetween('comments.created_at', [$from, $to])
            ->pluck('comments.created_at');

        // いいね/コメントの日時を「曜日×時間帯」のマスに振り分けて件数を+1する
        $increment = function ($timestamp) use (&$matrix, $slots) {
            $c = Carbon::parse($timestamp);
            $day = $c->dayOfWeekIso - 1; // 0 = Mon, 6 = Sun
            $hour = $c->hour;
            $slotIndex = null;
            foreach ($slots as $i => $slot) {
                if ($hour >= $slot['start'] && $hour <= $slot['end']) {
                    $slotIndex = $i;
                    break;
                }
            }
            if ($slotIndex === null) {
                return;
            }
            $matrix[$day][$slotIndex]++;
        };

        // likes/comments の日時ごとに、対応する曜日×時間帯のマスを +1 する
        // サンプル: likes ['2026-01-02 07:10', '2026-01-02 09:30'], comments ['2026-01-03 22:05']
        // → matrix[金曜][6-11] = 2, matrix[土曜][18-23] = 1
        foreach ($likeTimes as $ts) {
            $increment($ts);
        }
        foreach ($commentTimes as $ts) {
            $increment($ts);
        }

        // matrix を ECharts 用の [時間帯Idx, 曜日Idx, 件数] 配列に変換
        // 例: likes=2026-01-02 07:10/09:30（金曜 朝帯Idx=1に2件）、comments=2026-01-03 22:05（土曜 夜帯Idx=3に1件）
        //     曜日Idx: 0=月…6=日 / 時間帯Idx: 0=0-5,1=6-11,2=12-17,3=18-23
        //     → values に [1,4,2], [3,5,1] が入り、max は 2
        $values = [];
        $max = 0;
        foreach ($matrix as $dayIdx => $row) {
            foreach ($row as $slotIdx => $val) {
                $values[] = [$slotIdx, $dayIdx, $val];
                $max = max($max, $val);
            }
        }

        return [
            'days' => $days,
            'slots' => array_column($slots, 'label'),
            'values' => $values,
            'max' => $max,
        ];
    }

    private function buildFunnelData(User $user, Carbon $from, Carbon $to): array
    {
        $views = PostView::whereBetween('created_at', [$from, $to])
            ->whereHas('post', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->count();

        $likes = DB::table('likes')
            ->join('posts', 'likes.post_id', '=', 'posts.id')
            ->where('posts.user_id', $user->id)
            ->whereBetween('likes.created_at', [$from, $to])
            ->count();

        $comments = DB::table('comments')
            ->join('posts', 'comments.post_id', '=', 'posts.id')
            ->where('posts.user_id', $user->id)
            ->whereBetween('comments.created_at', [$from, $to])
            ->count();

        $totalReactions = $likes + $comments;
        $base = $views > 0 ? $views : 1; // 0除算回避

        $steps = [
            [
                'label' => '閲覧',
                'value' => $views,
                'progress' => 100,
            ],
            [
                'label' => 'いいね',
                'value' => $likes,
                'progress' => round(($likes / $base) * 100, 1),
            ],
            [
                'label' => 'コメント',
                'value' => $comments,
                'progress' => round(($comments / $base) * 100, 1),
            ],
            [
                'label' => '反応合計',
                'value' => $totalReactions,
                'progress' => round(($totalReactions / $base) * 100, 1),
            ],
        ];

        return [
            'steps' => $steps,
        ];
    }

    private function topPostsForUser(User $user, Carbon $from, Carbon $to, int $limit = 5): array
    {
        $posts = Post::where('user_id', $user->id)
            ->whereBetween('created_at', [$from, $to])
            ->withCount([
                'likedUsers as likes_in_range_count' => function ($query) use ($from, $to) {
                    $query->whereBetween('likes.created_at', [$from, $to]);
                },
                'comments as comments_in_range_count' => function ($query) use ($from, $to) {
                    $query->whereBetween('comments.created_at', [$from, $to]);
                },
            ])
            ->orderByRaw('(likes_in_range_count + comments_in_range_count) DESC')
            ->orderByDesc('likes_in_range_count')
            ->orderByDesc('comments_in_range_count')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        return $posts->map(function ($post) {
            $title = Str::limit($post->tweet ?? '', 80, '...');

            return [
                'id' => $post->id,
                'title' => $title,
                'likes' => (int) $post->likes_in_range_count,
                'comments' => (int) $post->comments_in_range_count,
            ];
        })->all();
    }

    private function normalizeRange(Request $request): array
    {
        $key = $request->query('range', '7d');
        $now = Carbon::now();

        return match ($key) {
            '30d' => [
                'key' => '30d',
                'from' => $now->copy()->subDays(30)->startOfDay(),
                'to' => $now->copy()->endOfDay(),
                'days' => 30,
            ],
            'custom' => $this->customRange($request, $now),
            default => [
                'key' => '7d',
                'from' => $now->copy()->subDays(7)->startOfDay(),
                'to' => $now->copy()->endOfDay(),
                'days' => 7,
            ],
        };
    }

    private function customRange(Request $request, Carbon $now): array
    {
        $fromInput = $request->query('from');
        $toInput = $request->query('to');

        $from = $this->parseDate($fromInput, $now->copy()->subDays(6)->toDateString());
        $to = $this->parseDate($toInput, $now->toDateString());

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to, $from];
        }

        $from = $from->startOfDay();
        $to = $to->endOfDay();

        $days = max((int) $from->diffInDays($to) + 1, 1);

        return [
            'key' => 'custom',
            'from' => $from,
            'to' => $to,
            'days' => $days,
        ];
    }

    private function parseDate(?string $value, string $fallback): Carbon
    {
        try {
            return Carbon::createFromFormat('Y-m-d', $value);
        } catch (\Throwable $e) {
            return Carbon::parse($fallback);
        }
    }
}
