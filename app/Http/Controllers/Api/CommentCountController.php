<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use OpenApi\Attributes as OA;
use Illuminate\Support\Facades\Log;

class CommentCountController extends Controller
{
    #[OA\Get(
        path: '/api/comments/count',
        summary: 'コメント全件数',
        description: 'コメント総数を返します',
        tags: ['comments'],
    )]
    #[OA\Response(
        response: 200,
        description: 'OK',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'count', type: 'integer', example: 123),
            ]
        )
    )]
    #[OA\Response(
        response: 500,
        description: 'Internal Server Error',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Server error'),
            ]
        )
    )]
    public function __invoke()
    {
        try {
            return response()->json([
                'count' => Comment::count(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to count comments', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Server error',
            ], 500);
        }
    }
}
