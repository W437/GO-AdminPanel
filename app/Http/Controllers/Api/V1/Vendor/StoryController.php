<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Story\StoreStoryRequest;
use App\Http\Requests\Story\UpdateStoryRequest;
use App\Http\Requests\Story\UploadStoryMediaRequest;
use App\Models\Restaurant;
use App\Models\Story;
use App\Services\StoryService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class StoryController extends Controller
{
    public function __construct(private readonly StoryService $storyService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $restaurant = $this->resolveRestaurant($request);

        $query = Story::with(['media'])
            ->ownedBy($restaurant->id)
            ->withCount([
                'views as view_count',
                'views as completed_view_count' => fn ($q) => $q->where('completed', true),
            ])
            ->orderByDesc('created_at');

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $perPage = $request->integer('per_page', 15);
        $stories = $query->paginate($perPage);

        return response()->json($stories);
    }

    public function store(StoreStoryRequest $request): JsonResponse
    {
        $restaurant = $this->resolveRestaurant($request);
        $data = $request->validated();

        $story = $this->storyService->createDraft($restaurant, $data);

        if (!empty($data['scheduled_for'])) {
            $publishAt = Carbon::parse($data['scheduled_for']);
            $story->forceFill([
                'status' => $publishAt->isFuture() ? Story::STATUS_SCHEDULED : Story::STATUS_PUBLISHED,
                'publish_at' => $publishAt,
                'expire_at' => $publishAt->copy()->addDay(),
            ])->save();
        }

        return response()->json([
            'message' => __('Story draft created.'),
            'story' => $story->fresh('media'),
        ], 201);
    }

    public function attachMedia(UploadStoryMediaRequest $request, int $storyId): JsonResponse
    {
        $restaurant = $this->resolveRestaurant($request);
        $story = $this->mustFindStory($request, $restaurant, $storyId);

        $media = $this->storyService->attachMedia(
            $story,
            $request->validated(),
            $request->file('media'),
            $request->file('thumbnail')
        );

        return response()->json([
            'message' => __('Story media uploaded.'),
            'media' => $media,
        ], 201);
    }

    public function update(UpdateStoryRequest $request, int $storyId): JsonResponse
    {
        $restaurant = $this->resolveRestaurant($request);
        $story = $this->mustFindStory($request, $restaurant, $storyId);
        $data = $request->validated();

        if (array_key_exists('title', $data)) {
            $story->title = $data['title'];
        }

        $publishNow = $request->boolean('publish_now');
        $publishAtInput = $data['publish_at'] ?? null;

        if ($publishNow || $publishAtInput) {
            $publishAt = $publishAtInput ? Carbon::parse($publishAtInput) : Carbon::now();
            $this->storyService->publish($story, $publishAt);
        } elseif (($data['status'] ?? null) === Story::STATUS_DRAFT) {
            $this->storyService->markDraft($story);
        }

        $story->save();

        if (array_key_exists('overlays', $data)) {
            $story = $this->storyService->updateOverlays($story, $data['overlays']);
        }

        $metadata = Arr::only($data, ['type', 'media_url', 'thumbnail_url', 'duration_seconds']);

        if (!empty($metadata)) {
            $story = $this->storyService->updateStoryMediaMetadata($story, $metadata);
        }

        return response()->json([
            'message' => __('Story updated.'),
            'story' => $story->fresh('media'),
        ]);
    }

    public function destroy(Request $request, int $storyId): JsonResponse
    {
        $restaurant = $this->resolveRestaurant($request);
        $story = $this->mustFindStory($request, $restaurant, $storyId);

        $this->storyService->delete($story);

        return response()->json([
            'message' => __('Story removed.'),
        ]);
    }

    public function deleteMedia(Request $request, int $storyId, int $mediaId): JsonResponse
    {
        $restaurant = $this->resolveRestaurant($request);
        $story = $this->mustFindStory($request, $restaurant, $storyId);
        $media = $story->media()->where('id', $mediaId)->firstOrFail();

        $this->storyService->deleteMedia($media);

        return response()->json([
            'message' => __('Story media removed.'),
        ]);
    }

    protected function resolveRestaurant(Request $request): Restaurant
    {
        if (!config('stories.enabled')) {
            abort(403, 'Stories feature disabled.');
        }

        $vendor = $request['vendor'];

        if (!$vendor || !$vendor->restaurants || count($vendor->restaurants) === 0) {
            abort(404, 'Restaurant not found.');
        }

        /** @var Restaurant $restaurant */
        $restaurant = $vendor->restaurants[0];

        if (!$restaurant->stories_enabled) {
            throw ValidationException::withMessages([
                'restaurant' => __('Stories are disabled for this restaurant.'),
            ]);
        }

        return $restaurant;
    }

    protected function mustFindStory(Request $request, Restaurant $restaurant, int $storyId): Story
    {
        $story = Story::with('media')->ownedBy($restaurant->id)->findOrFail($storyId);
        $actor = $request['vendor_employee'] ?? $request['vendor'];

        if ($actor) {
            Gate::forUser($actor)->authorize('manage', $story);
        }

        return $story;
    }
}
