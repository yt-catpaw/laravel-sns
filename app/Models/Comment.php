<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $post_id
 * @property int $user_id
 * @property int|null $parent_id
 * @property string $body
 * @property-read \App\Models\Post $post
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\CommentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Models\Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Models\Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|\App\Models\Comment query()
 * @mixin \Eloquent
 */
class Comment extends Model
{
    /** @use HasFactory<\Database\Factories\CommentFactory> */
    use HasFactory;


    protected $fillable = [
        'post_id',
        'user_id',
        'body',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
