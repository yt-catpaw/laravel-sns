<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $tweet
 * @property string|null $image_path
 * @property int $user_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $likedUsers
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\PostFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Models\Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Models\Post newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Models\Post query()
 * @mixin \Eloquent
 */
class Post extends Model
{
    use HasFactory;

    /**
     * 投稿で更新可能なカラム
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'tweet',
        'image_path',
    ];

    /**
     * 投稿者
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likedUsers()
    {
        return $this->belongsToMany(User::class, 'likes')
            ->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
