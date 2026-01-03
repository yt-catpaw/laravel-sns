<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AnalyticsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function 過去7日間の集計が表示される(): void
    {
        Carbon::setTestNow(Carbon::parse('2024-01-10 12:00:00'));

        $user = User::factory()->create();
        $likerA = User::factory()->create();
        $likerB = User::factory()->create();

        $recentPost1 = Post::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(3),
            'updated_at' => now()->subDays(3),
        ]);
        $recentPost2 = Post::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(5),
        ]);

        $oldPost = Post::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(10),
        ]);

        $recentPost1->likedUsers()->attach([$likerA->id, $likerB->id]);
        $recentPost2->likedUsers()->attach([$likerA->id]);
        // 7日より前なので集計対象外
        $oldPost->likedUsers()->attach([$likerA->id]);

        Comment::factory()->create([
            'user_id' => $likerA->id,
            'post_id' => $recentPost1->id,
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);
        Comment::factory()->create([
            'user_id' => $likerB->id,
            'post_id' => $recentPost2->id,
            'created_at' => now()->subDays(1),
            'updated_at' => now()->subDays(1),
        ]);
        Comment::factory()->create([
            'user_id' => $likerA->id,
            'post_id' => $oldPost->id,
            'created_at' => now()->subDays(9),
            'updated_at' => now()->subDays(9),
        ]); // 7日より前なので集計対象外

        $response = $this->actingAs($user)->get(route('analytics.index'));

        $response->assertOk();
        $response->assertViewHas('summary', function ($summary) {
            // posts_count: 2（10日前の投稿は期間外）
            // likes_received: 2 + 1 = 3
            // comments_received: 1 + 1 = 2
            // reaction_rate: ((3 + 2) / 2) * 100 = 250.0
            // posts_daily_avg: 2 / 7 = 0.2857... -> 0.3
            return $summary['posts_count'] === 2
                && $summary['likes_received'] === 3
                && $summary['comments_received'] === 2
                && $summary['reaction_rate'] === 250.0
                && $summary['posts_daily_avg'] === 0.3;
        });
        $response->assertViewHas('range_key', '7d');
        $response->assertViewHas('range_days', 7);
    }

    #[Test]
    public function 過去30日間の集計が表示される(): void
    {
        Carbon::setTestNow(Carbon::parse('2024-01-10 12:00:00'));

        $user = User::factory()->create();
        $liker = User::factory()->create();

        $recentPost = Post::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(3),
            'updated_at' => now()->subDays(3),
        ]);
        $oldPostWithin30Days = Post::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(20),
            'updated_at' => now()->subDays(20),
        ]);
        $tooOldPost = Post::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(40),
            'updated_at' => now()->subDays(40),
        ]);

        $recentPost->likedUsers()->attach([$liker->id]);
        $oldPostWithin30Days->likedUsers()->attach([$liker->id]);
        $tooOldPost->likedUsers()->attach([$liker->id]); // 30日より前なので集計対象外

        Comment::factory()->create([
            'user_id' => $liker->id,
            'post_id' => $recentPost->id,
            'created_at' => now()->subDays(1),
            'updated_at' => now()->subDays(1),
        ]);

        $response = $this->actingAs($user)->get(route('analytics.index', ['range' => '30d']));

        $response->assertOk();
        $response->assertViewHas('summary', function ($summary) {
            // posts_count: 2（40日前の投稿は期間外）
            // likes_received: 1 + 1 = 2
            // comments_received: 1
            // reaction_rate: ((2 + 1) / 2) * 100 = 150.0
            // posts_daily_avg: 2 / 30 = 0.066... -> 0.1
            return $summary['posts_count'] === 2
                && $summary['likes_received'] === 2
                && $summary['comments_received'] === 1
                && $summary['reaction_rate'] === 150.0
                && $summary['posts_daily_avg'] === 0.1;
        });
        $response->assertViewHas('range_key', '30d');
        $response->assertViewHas('range_days', 30);
    }

    #[Test]
    public function カスタム日付範囲の集計が表示される(): void
    {
        Carbon::setTestNow(Carbon::parse('2024-01-10 12:00:00'));

        $user = User::factory()->create();
        $likerA = User::factory()->create();
        $likerB = User::factory()->create();

        // カスタム範囲: 2024-01-01 〜 2024-01-05
        $postJan2 = Post::factory()->create([
            'user_id' => $user->id,
            'created_at' => '2024-01-02 10:00:00',
            'updated_at' => '2024-01-02 10:00:00',
        ]);
        $postJan5 = Post::factory()->create([
            'user_id' => $user->id,
            'created_at' => '2024-01-05 09:00:00',
            'updated_at' => '2024-01-05 09:00:00',
        ]);
        $oldPost = Post::factory()->create([
            'user_id' => $user->id,
            'created_at' => '2023-12-20 09:00:00',
            'updated_at' => '2023-12-20 09:00:00',
        ]); // 期間外

        $postJan2->likedUsers()->attach([$likerA->id, $likerB->id]);
        $postJan5->likedUsers()->attach([$likerA->id]);
        $oldPost->likedUsers()->attach([$likerA->id]); // 期間外

        Comment::factory()->create([
            'user_id' => $likerA->id,
            'post_id' => $postJan2->id,
            'created_at' => '2024-01-03 12:00:00',
            'updated_at' => '2024-01-03 12:00:00',
        ]);

        $response = $this->actingAs($user)->get(route('analytics.index', [
            'range' => 'custom',
            'from' => '2024-01-01',
            'to' => '2024-01-05',
        ]));

        $response->assertOk();
        $response->assertViewHas('summary', function ($summary) {
            // posts_count: 2（12/20の投稿は期間外）
            // likes_received: 2 + 1 = 3
            // comments_received: 1
            // reaction_rate: ((3 + 1) / 2) * 100 = 200.0
            // posts_daily_avg: 2 / 5 = 0.4
            return $summary['posts_count'] === 2
                && $summary['likes_received'] === 3
                && $summary['comments_received'] === 1
                && $summary['reaction_rate'] === 200.0
                && $summary['posts_daily_avg'] === 0.4;
        });
        $response->assertViewHas('range_key', 'custom');
        $response->assertViewHas('range_days', 5);
        $response->assertViewHas('range_from', '2024-01-01');
        $response->assertViewHas('range_to', '2024-01-05');
    }
}
