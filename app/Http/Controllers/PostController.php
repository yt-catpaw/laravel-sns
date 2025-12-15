<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user')
            ->latest()
            ->get();

        return view('timeline.index', compact('posts'));
    }

    public function store(PostRequest $request)
    {
        $data = $request->validated();

        $imagePath = null;

        try {
            DB::transaction(function () use ($request, $data, &$imagePath) {
                if ($request->hasFile('image')) {
                    $imagePath = Storage::putFile('public/posts/images', $request->file('image'));
                }

                Post::create([
                    'user_id' => $request->user()->id,
                    'tweet' => $data['tweet'],
                    'image_path' => $imagePath,
                ]);
            });
        } catch (\Throwable $e) {
            if ($imagePath) {
                Storage::delete($imagePath);
            }
            throw $e;
        }

        return redirect()->back()->with('status', '投稿しました。');
    }
}
