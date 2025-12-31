<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Post;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(Post $post, StoreCommentRequest $request)
    {
        $post = Post::find($post->id);

        if (!$post) {
            return redirect()->route('timeline.index')
                ->with('status', '投稿が削除されていました');
        }

        Comment::create([
            'post_id'  => $post->id,
            'user_id'  => $request->user()->id,
            'body'     => $request->validated()['body'],
        ]);

        return back()->with('status', '返信しました');
    }

    public function destroy(Comment $comment)
    {
        if (auth()->id() !== $comment->user_id) {
            abort(403);
        }

        $comment->delete();

        return back()->with('status', 'コメントを削除しました');
    }
}
