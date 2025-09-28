<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class StoryMedia extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'duration_seconds' => 'integer',
    ];

    protected $appends = ['media_url', 'thumbnail_url'];

    public function story()
    {
        return $this->belongsTo(Story::class);
    }

    public function getMediaUrlAttribute(): ?string
    {
        return $this->resolveDiskUrl($this->media_path);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->resolveDiskUrl($this->thumbnail_path);
    }

    protected function resolveDiskUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $disk = config('stories.media_disk', config('filesystems.default'));

        return Storage::disk($disk)->url($path);
    }
}
