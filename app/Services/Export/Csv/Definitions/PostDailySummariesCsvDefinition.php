<?php

namespace App\Services\Export\Csv\Definitions;

use App\Models\PostDailySummary;
use App\Services\Export\Csv\CsvDefinition;

class PostDailySummariesCsvDefinition implements CsvDefinition
{
    public function __construct(
        private readonly ?string $from = null,
        private readonly ?string $to = null,
    ) {
    }

    public function headers(): array
    {
        return ['日付', 'ユーザーID', '投稿数', 'いいね数', 'コメント数'];
    }

    public function rows(): iterable
    {
        $query = PostDailySummary::orderByDesc('date')->orderByDesc('user_id');

        if ($this->from && $this->to) {
            $query->whereBetween('date', [$this->from, $this->to]);
        } elseif ($this->from) {
            $query->whereDate('date', '>=', $this->from);
        } elseif ($this->to) {
            $query->whereDate('date', '<=', $this->to);
        }

        foreach ($query->cursor() as $summary) {
            yield [
                $summary->date,
                $summary->user_id,
                $summary->posts_count,
                $summary->likes_received,
                $summary->comments_received,
            ];
        }
    }
}
