<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $post_id
 * @property int|null $user_id
 * @property string|null $session_token
 * @property-read \App\Models\Post $post
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\PostViewFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Models\PostView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Models\PostView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Models\PostView query()
 * @mixin \Eloquent
 */
class PostView extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_id',
        'session_token',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
