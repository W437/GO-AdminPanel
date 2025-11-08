<?php

namespace App\Http\Controllers\Vendor;

use App\CentralLogics\Helpers;
use App\Models\Review;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'start_date' => ['nullable','date_format:Y-m-d'],
            'end_date'   => ['nullable','date_format:Y-m-d'],
            'rating'     => ['nullable','integer','min:1','max:5'],
            'reply_status' => ['nullable','array'],
            'reply_status.*' => ['in:replied,no_reply'],
        ]);

        $reviewsQuery = Review::with(['food','customer'])
            ->whereHas('food', function($query){
                return $query->where('restaurant_id', Helpers::get_restaurant_id());
            });

        // Date range filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            try {
                $start = Carbon::createFromFormat('Y-m-d', $request->input('start_date'))->startOfDay();
                $end   = Carbon::createFromFormat('Y-m-d', $request->input('end_date'))->endOfDay();
                if ($start->lessThanOrEqualTo($end)) {
                    $reviewsQuery->whereBetween('created_at', [$start, $end]);
                }
            } catch (\Exception $e) {
                // Ignore invalid date silently; validation already ensures format
            }
        }

        // Rating filter
        if ($request->filled('rating')) {
            $rating = (int) $request->input('rating');
            if ($rating === 5) {
                $reviewsQuery->where('rating', 5);
            } else {
                $reviewsQuery->where('rating', '>=', $rating);
            }
        }

        // Reply status filter
        $replyStatuses = $request->input('reply_status');
        if (is_array($replyStatuses)) {
            $hasReplied = in_array('replied', $replyStatuses);
            $hasNoReply = in_array('no_reply', $replyStatuses);

            if ($hasReplied && !$hasNoReply) {
                $reviewsQuery->whereNotNull('reply')->where('reply', '!=', '');
            } elseif ($hasNoReply && !$hasReplied) {
                $reviewsQuery->where(function($q){
                    $q->whereNull('reply')->orWhere('reply', '=','');
                });
            }
            // If both or none are selected, do not apply reply filter
        }

        $reviews = $reviewsQuery->latest()->paginate(config('default_pagination'));
        return view('vendor-views.review.index', compact('reviews'));
    }

    public function update_reply(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|max:255',
        ]);

        $review = Review::findOrFail($id);
        $review->reply = $request->reply;
        $review->restaurant_id = Helpers::get_restaurant_id();
        $review->save();

        Toastr::success(translate('messages.review_reply_updated'));
        return redirect()->route('vendor.reviews');
    }
}
