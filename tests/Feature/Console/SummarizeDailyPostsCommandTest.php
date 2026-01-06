<?php

namespace Tests\Feature\Console;

use App\Models\Post;
use App\Models\PostDailySummary;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class SummarizeDailyPostsCommandTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function 指定日の投稿いいねコメントを集計して保存する(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $liker2 = User::factory()->create();
        $likerOld = User::factory()->create();

        Carbon::setTestNow('2026-01-10 12:00:00');
        $targetDate = Carbon::parse('2026-01-09');

        // 対象日の投稿
        $postA = Post::factory()->for($user)->create([
            'created_at' => $targetDate->copy()->setTime(10, 0),
            'updated_at' => $targetDate->copy()->setTime(10, 0),
        ]);
        // 対象日の別ユーザー投稿
        $postB = Post::factory()->for($other)->create([
            'created_at' => $targetDate->copy()->setTime(11, 0),
            'updated_at' => $targetDate->copy()->setTime(11, 0),
        ]);
        // 対象外（日付が前）の投稿
        Post::factory()->for($user)->create([
            'created_at' => $targetDate->copy()->subDay()->setTime(9, 0),
            'updated_at' => $targetDate->copy()->subDay()->setTime(9, 0),
        ]);

        // いいね（対象日）2件
        $postA->likedUsers()->attach([$other->id], ['created_at' => $targetDate->copy()->setTime(12, 0)]);
        $postA->likedUsers()->attach([$liker2->id], ['created_at' => $targetDate->copy()->setTime(13, 0)]);
        // 対象外のいいね
        $postA->likedUsers()->attach([$likerOld->id], ['created_at' => $targetDate->copy()->subDay()->setTime(8, 0)]);

        // コメント（対象日）1件
        $postA->comments()->create([
            'user_id' => $other->id,
            'body' => 'target comment',
            'created_at' => $targetDate->copy()->setTime(14, 0),
            'updated_at' => $targetDate->copy()->setTime(14, 0),
        ])->forceFill([
            'created_at' => $targetDate->copy()->setTime(14, 0),
            'updated_at' => $targetDate->copy()->setTime(14, 0),
        ])->saveQuietly();
        // 対象外コメント
        $postA->comments()->create([
            'user_id' => $other->id,
            'body' => 'old',
            'created_at' => $targetDate->copy()->subDay()->setTime(7, 0),
            'updated_at' => $targetDate->copy()->subDay()->setTime(7, 0),
        ])->forceFill([
            'created_at' => $targetDate->copy()->subDay()->setTime(7, 0),
            'updated_at' => $targetDate->copy()->subDay()->setTime(7, 0),
        ])->saveQuietly();

        Artisan::call('summary:posts-daily', ['--date' => $targetDate->toDateString()]);

        $summary = PostDailySummary::where('user_id', $user->id)
            ->where('date', $targetDate->toDateString())
            ->first();

        $this->assertNotNull($summary);
        $this->assertSame(1, $summary->posts_count);
        $this->assertSame(2, $summary->likes_received);
        $this->assertSame(1, $summary->comments_received);

        $summaryOther = PostDailySummary::where('user_id', $other->id)
            ->where('date', $targetDate->toDateString())
            ->first();
        $this->assertNotNull($summaryOther);
        $this->assertSame(1, $summaryOther->posts_count);
        $this->assertSame(0, $summaryOther->likes_received);
        $this->assertSame(0, $summaryOther->comments_received);
    }
}
