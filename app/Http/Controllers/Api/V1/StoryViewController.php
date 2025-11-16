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

        // Auto-generate session key for anonymous users if not provided
        $sessionKey = $data['session_key'] ?? null;
        if (!$user && !$sessionKey) {
            $sessionKey = $this->generateAnonymousSessionKey($request);
        }

        $story = Story::with('restaurant')->findOrFail($storyId);

        if (!$story->isActive()) {
            abort(404);
        }

        $view = $this->storyService->recordView(
            $story,
            $user,
            $sessionKey,
            (bool) ($data['completed'] ?? false)
        );

        return response()->json([
            'message' => __('Story view recorded.'),
            'view_id' => $view->id,
        ], 200);
    }

    /**
     * Generate a session key for anonymous users based on IP and User-Agent
     */
    protected function generateAnonymousSessionKey(Request $request): string
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent() ?? 'unknown';

        // Create a stable session key that's consistent for the same IP + User-Agent
        // but changes daily to allow tracking across sessions while respecting privacy
        $date = date('Y-m-d');
        return 'anon_' . hash('sha256', $ip . $userAgent . $date);
    }
}
