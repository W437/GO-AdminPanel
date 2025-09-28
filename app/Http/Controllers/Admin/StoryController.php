<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Story;
use App\Services\StoryService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class StoryController extends Controller
{
    public function __construct(private readonly StoryService $storyService)
    {
    }

    public function index(Request $request)
    {
        $query = Story::with(['restaurant:id,name,stories_enabled,zone_id', 'media'])
            ->withCount([
                'views as view_count',
                'views as completed_view_count' => fn ($q) => $q->where('completed', true),
            ])
            ->orderByDesc('publish_at')
            ->orderByDesc('id');

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($restaurantId = $request->query('restaurant_id')) {
            $query->where('restaurant_id', $restaurantId);
        }

        $stories = $query->paginate(25)->appends($request->query());
        $restaurants = Restaurant::select('id', 'name')->orderBy('name')->get();

        return view('admin-views.stories.index', compact('stories', 'restaurants'));
    }

    public function expire(Story $story)
    {
        $this->storyService->expire($story);
        Toastr::success(__('Story marked as expired.'));

        return back();
    }

    public function destroy(Story $story)
    {
        $this->storyService->delete($story);
        Toastr::success(__('Story removed.'));

        return back();
    }

    public function toggleRestaurant(Restaurant $restaurant)
    {
        $restaurant->stories_enabled = !$restaurant->stories_enabled;
        $restaurant->save();

        $message = $restaurant->stories_enabled
            ? __('Stories enabled for the restaurant.')
            : __('Stories disabled for the restaurant.');

        Toastr::success($message);

        return back();
    }
}
