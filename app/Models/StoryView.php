<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class StoryView extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'completed' => 'boolean',
        'viewed_at' => 'datetime',
    ];

    public function story()
    {
        return $this->belongsTo(Story::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function scopeForStory(Builder $query, Story $story): Builder
    {
        return $query->where('story_id', $story->id);
    }

    public static function record(Story $story, ?User $customer, ?string $sessionKey, bool $completed = false): self
    {
        $viewerKey = static::makeViewerKey($customer, $sessionKey);

        return static::updateOrCreate(
            [
                'story_id' => $story->id,
                'viewer_key' => $viewerKey,
            ],
            [
                'customer_id' => $customer?->id,
                'session_key' => $sessionKey,
                'viewed_at' => Carbon::now(),
                'completed' => $completed ? true : false,
            ]
        );
    }

    public static function makeViewerKey(?User $customer, ?string $sessionKey): string
    {
        if ($customer) {
            return 'user:' . $customer->id;
        }

        if ($sessionKey) {
            return 'guest:' . sha1($sessionKey);
        }

        return 'guest:' . Str::orderedUuid()->toString();
    }
}
