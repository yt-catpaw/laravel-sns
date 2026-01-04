<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

        return view('analytics.index', [
            'summary' => $summary,
            'range_key' => $range['key'],
            'range_days' => $range['days'],
            'range_from' => $range['from']->toDateString(),
            'range_to' => $range['to']->toDateString(),
            'top_posts' => $topPosts,
            'trend_data' => $trendData,
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
