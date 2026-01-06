<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $date
 * @property int $posts_count
 * @property int $likes_received
 * @property int $comments_received
 * @property int $user_id
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Models\PostDailySummary newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Models\PostDailySummary newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Models\PostDailySummary query()
 * @mixin \Eloquent
 */
class PostDailySummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'user_id',
        'posts_count',
        'likes_received',
        'comments_received',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
