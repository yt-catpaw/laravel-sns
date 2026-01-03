<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AnalyticsController extends Controller
{
    public function show(Request $request)
    {
        $user = auth()->user();

        ['key' => $rangeKey, 'days' => $rangeDays] = $this->normalizeRange(
            $request->query('range', '7d')
        );

        $summary = $this->buildSummaryForUser($user, $rangeDays);

        return view('analytics.index', [
            'summary' => $summary,
            'range_key' => $rangeKey,
            'range_days' => $rangeDays,
        ]);
    }

    private function buildSummaryForUser(User $user, int $rangeDays): array
    {
        $to = Carbon::now();
        $from = $to->copy()->subDays($rangeDays);

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

    private function normalizeRange(string $range): array
    {
        return match ($range) {
            '30d' => ['key' => '30d', 'days' => 30],
            default => ['key' => '7d', 'days' => 7],
        };
    }
}
