<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class LikeController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $user = $request->user();

        try {
            $user->likedPosts()->attach($post->id);
        } catch (QueryException $e) {
            // UNIQUE制約違反（すでにLike済み）でも、状態を返す
        }

        $count = $post->likedUsers()->count();

        return response()->json([
            'liked' => true,
            'count' => $count,
        ], 200);
    }

    public function destroy(Request $request, Post $post)
    {
        $user = $request->user();

        $user->likedPosts()->detach($post->id);

        $count = $post->likedUsers()->count();

        return response()->json([
            'liked' => false,
            'count' => $count,
        ], 200);
    }
}
