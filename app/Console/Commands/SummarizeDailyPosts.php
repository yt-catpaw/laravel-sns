<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\PostDailySummary;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SummarizeDailyPosts extends Command
{
    protected $signature = 'summary:posts-daily {--date=}';

    protected $description = '指定日の投稿/いいね/コメント件数を post_daily_summaries に保存します（デフォルトは前日）。';

    public function handle(): int
    {
        $dateInput = $this->option('date');
        $target = $dateInput ? Carbon::parse($dateInput) : Carbon::yesterday();
        $from = $target->copy()->startOfDay();
        $to = $target->copy()->endOfDay();

        $postsCounts = Post::select('user_id', DB::raw('COUNT(*) as c'))
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('user_id')
            ->pluck('c', 'user_id');

        $likesCounts = DB::table('likes')
            ->join('posts', 'likes.post_id', '=', 'posts.id')
            ->whereBetween('likes.created_at', [$from, $to])
            ->select('posts.user_id', DB::raw('COUNT(*) as c'))
            ->groupBy('posts.user_id')
            ->pluck('c', 'posts.user_id');

        $commentsCounts = DB::table('comments')
            ->join('posts', 'comments.post_id', '=', 'posts.id')
            ->whereBetween('comments.created_at', [$from, $to])
            ->select('posts.user_id', DB::raw('COUNT(*) as c'))
            ->groupBy('posts.user_id')
            ->pluck('c', 'posts.user_id');

        $userIds = $postsCounts->keys()
            ->merge($likesCounts->keys())
            ->merge($commentsCounts->keys())
            ->unique()
            ->values();

        $total = 0;
        foreach ($userIds as $userId) {
            try {
                PostDailySummary::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'date' => $target->toDateString(),
                    ],
                    [
                        'posts_count' => (int) ($postsCounts[$userId] ?? 0),
                        'likes_received' => (int) ($likesCounts[$userId] ?? 0),
                        'comments_received' => (int) ($commentsCounts[$userId] ?? 0),
                    ]
                );
                $total++;
            } catch (\Throwable $e) {
                Log::error('PostDailySummary failed', [
                    'user_id' => $userId,
                    'date' => $target->toDateString(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("{$target->toDateString()} のサマリーを {$total} ユーザー分保存しました。");

        return self::SUCCESS;
    }
}
