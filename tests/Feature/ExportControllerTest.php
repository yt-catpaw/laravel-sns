<?php

namespace Tests\Feature;

use App\Models\PostDailySummary;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExportControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function 日次サマリーをCSVでダウンロードできる(): void
    {
        $user = User::factory()->create();

        PostDailySummary::create([
            'date' => '2026-01-05',
            'user_id' => $user->id,
            'posts_count' => 2,
            'likes_received' => 5,
            'comments_received' => 3,
        ]);
        PostDailySummary::create([
            'date' => '2026-01-04',
            'user_id' => $user->id,
            'posts_count' => 1,
            'likes_received' => 1,
            'comments_received' => 0,
        ]);

        $response = $this->actingAs($user)->get('/exports/daily-summaries');

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $response->assertHeader('content-disposition', 'attachment; filename=post_daily_summaries.csv');

        $rawLines = array_map('trim', explode("\n", $response->streamedContent()));
        $rawLines = array_values(array_filter($rawLines, fn ($line) => $line !== ''));
        $lines = array_map(function ($line) {
            $line = ltrim($line, "\xEF\xBB\xBF");
            return str_getcsv($line);
        }, $rawLines);

        $this->assertSame(['日付', 'ユーザーID', '投稿数', 'いいね数', 'コメント数'], $lines[0]);
        $this->assertSame(['2026-01-05', (string) $user->id, '2', '5', '3'], $lines[1]);
        $this->assertSame(['2026-01-04', (string) $user->id, '1', '1', '0'], $lines[2]);
    }

    #[Test]
    public function 日付範囲でCSVを絞り込める(): void
    {
        $user = User::factory()->create();

        PostDailySummary::create([
            'date' => '2026-01-05',
            'user_id' => $user->id,
            'posts_count' => 2,
            'likes_received' => 5,
            'comments_received' => 3,
        ]);
        PostDailySummary::create([
            'date' => '2026-01-04',
            'user_id' => $user->id,
            'posts_count' => 1,
            'likes_received' => 1,
            'comments_received' => 0,
        ]);

        $response = $this->actingAs($user)->get('/exports/daily-summaries?from=2026-01-05&to=2026-01-05');

        $rawLines = array_map('trim', explode("\n", $response->streamedContent()));
        $rawLines = array_values(array_filter($rawLines, fn ($line) => $line !== ''));
        $lines = array_map(function ($line) {
            $line = ltrim($line, "\xEF\xBB\xBF");
            return str_getcsv($line);
        }, $rawLines);

        $this->assertSame(['日付', 'ユーザーID', '投稿数', 'いいね数', 'コメント数'], $lines[0]);
        $this->assertCount(2, $lines); // header + 1 row
        $this->assertSame(['2026-01-05', (string) $user->id, '2', '5', '3'], $lines[1]);
    }
}
