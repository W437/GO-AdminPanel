<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Story extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'publish_at' => 'datetime',
        'expire_at' => 'datetime',
        'overlays' => 'array',
        'has_overlays' => 'boolean',
    ];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_DELETED = 'deleted';

    public function media()
    {
        return $this->hasMany(StoryMedia::class)->orderBy('sequence');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function views()
    {
        return $this->hasMany(StoryView::class);
    }

    public function scopeOwnedBy(Builder $query, int $restaurantId): Builder
    {
        return $query->where('restaurant_id', $restaurantId);
    }

    public function scopeActive(Builder $query): Builder
    {
        $now = CarbonImmutable::now();

        return $query
            ->where('status', self::STATUS_PUBLISHED)
            ->whereNotNull('publish_at')
            ->where('publish_at', '<=', $now)
            ->where(function (Builder $inner) use ($now) {
                $inner->whereNull('expire_at')->orWhere('expire_at', '>', $now);
            });
    }

    public function scopeForFeed(Builder $query, ?int $zoneId = null): Builder
    {
        if ($zoneId) {
            $query->whereHas('restaurant', function (Builder $restaurantQuery) use ($zoneId) {
                $restaurantQuery->where('zone_id', $zoneId);
            });
        }

        return $query->orderByDesc('publish_at');
    }

    public function isActive(): bool
    {
        if ($this->status !== self::STATUS_PUBLISHED || !$this->publish_at) {
            return false;
        }

        $now = CarbonImmutable::now();

        if ($this->publish_at->gt($now)) {
            return false;
        }

        return $this->expire_at === null || $this->expire_at->gt($now);
    }

    public function getOverlaysAttribute($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if ($value === null) {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }
}
