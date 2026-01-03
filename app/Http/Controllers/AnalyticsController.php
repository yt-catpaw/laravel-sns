<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;

class AnalyticsController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        $summary = $this->buildSummaryForUser($user);

        return view('analytics.index', compact('summary'));
    }

    private function buildSummaryForUser(User $user): array
    {
        $posts = Post::where('user_id', $user->id)
            ->withCount(['likedUsers', 'comments'])
            ->get();

        $postsCount = $posts->count();
        $likesReceived = $posts->sum('liked_users_count');
        $commentsReceived = $posts->sum('comments_count');
        $reactionRate = $postsCount > 0
            ? round((($likesReceived + $commentsReceived) / $postsCount) * 100, 1)
            : 0;

        return [
            'posts_count' => $postsCount,
            'likes_received' => $likesReceived,
            'comments_received' => $commentsReceived,
            'reaction_rate' => $reactionRate,
        ];
    }
}
