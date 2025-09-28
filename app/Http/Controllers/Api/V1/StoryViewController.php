<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Story;
use App\Services\StoryService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StoryViewController extends Controller
{
    public function __construct(private readonly StoryService $storyService)
    {
    }

    public function store(Request $request, int $storyId)
    {
        if (!config('stories.enabled')) {
            abort(404);
        }

        $data = $request->validate([
            'session_key' => ['nullable', 'string', 'max:191'],
            'completed' => ['nullable', 'boolean'],
        ]);

        $user = $request->user() ?: auth('api')->user();

        if (!$user && empty($data['session_key'])) {
            throw ValidationException::withMessages([
                'session_key' => __('Provide either an authenticated user or a session key to record the view.'),
            ]);
        }

        $story = Story::with('restaurant')->findOrFail($storyId);

        if (!$story->isActive()) {
            abort(404);
        }

        $view = $this->storyService->recordView(
            $story,
            $user,
            $data['session_key'] ?? null,
            (bool) ($data['completed'] ?? false)
        );

        return response()->json([
            'message' => __('Story view recorded.'),
            'view_id' => $view->id,
        ], 200);
    }
}
