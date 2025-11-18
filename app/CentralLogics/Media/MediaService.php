<?php

namespace App\CentralLogics\Media;

use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaService
{
    public static function getDisk()
    {
        $config = Helpers::get_business_settings('local_storage');

        return isset($config) ? ($config == 0 ? 's3' : 'public') : 'public';
    }

    public static function upload(string $dir, string $format, $image = null)
    {
        try {
            if ($image != null) {
                $imageName = \Carbon\Carbon::now()->toDateString() . "-" . uniqid() . "." . $format;
                $disk = self::getDisk();

                if (!Storage::disk($disk)->exists($dir)) {
                    Storage::disk($disk)->makeDirectory($dir);
                }

                if ($disk === 's3') {
                    Storage::disk($disk)->put($dir . $imageName, file_get_contents($image->getRealPath()), 'public');
                } else {
                    Storage::disk($disk)->putFileAs($dir, $image, $imageName);
                }
            } else {
                $imageName = 'def.png';
            }
        } catch (\Exception $e) {
            Log::error('S3 Upload Error', [
                'disk' => self::getDisk(),
                'dir' => $dir,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine()
            ]);
            $imageName = 'def.png';
        }
        return $imageName ?? 'def.png';
    }

    public static function update(string $dir, $old_image, string $format, $image = null)
    {
        if ($image == null) {
            return $old_image;
        }
        try {
            if (Storage::disk(self::getDisk())->exists($dir . $old_image)) {
                Storage::disk(self::getDisk())->delete($dir . $old_image);
            }
        } catch (\Exception $e) {
        }
        return self::upload($dir, $format, $image);
    }

    public static function check_and_delete(string $dir, $old_image)
    {
        try {
            if (Storage::disk('public')->exists($dir . $old_image)) {
                Storage::disk('public')->delete($dir . $old_image);
            }
            if (Storage::disk('s3')->exists($dir . $old_image)) {
                Storage::disk('s3')->delete($dir . $old_image);
            }
        } catch (\Exception $e) {
        }

        return true;
    }

    public static function generate_video_thumbnail(string $dir, string $video_filename)
    {
        try {
            $disk = self::getDisk();
            $video_path = Storage::disk($disk)->path($dir . $video_filename);

            $thumbnail_filename = pathinfo($video_filename, PATHINFO_FILENAME) . '-thumb.jpg';
            $thumbnail_path = Storage::disk($disk)->path($dir . $thumbnail_filename);

            $thumbnail_dir = dirname($thumbnail_path);
            if (!is_dir($thumbnail_dir)) {
                mkdir($thumbnail_dir, 0755, true);
            }

            $ffmpeg_command = sprintf(
                'ffmpeg -i %s -ss 00:00:00.1 -vframes 1 -q:v 2 %s 2>&1',
                escapeshellarg($video_path),
                escapeshellarg($thumbnail_path)
            );

            exec($ffmpeg_command, $output, $return_var);

            if ($return_var !== 0) {
                Log::error('FFmpeg thumbnail generation failed', [
                    'command' => $ffmpeg_command,
                    'output' => $output,
                    'return_var' => $return_var
                ]);
                return false;
            }

            return $thumbnail_filename;
        } catch (\Exception $exception) {
            Log::error('Video thumbnail generation failed', ['error' => $exception->getMessage()]);
            return false;
        }
    }

    public static function generate_blurhash(string $dir, string $image_filename, int $components_x = 5, int $components_y = 4)
    {
        try {
            $disk = self::getDisk();
            $image_path = Storage::disk($disk)->path($dir . $image_filename);

            if (!file_exists($image_path)) {
                return null;
            }

            $image_data = file_get_contents($image_path);
            $image_source = imagecreatefromstring($image_data);

            if (!$image_source) {
                return null;
            }

            $width = imagesx($image_source);
            $height = imagesy($image_source);
            $aspect_ratio = $width / $height;

            if ($aspect_ratio > 1) {
                $scaled_width = 400;
                $scaled_height = intval(400 / $aspect_ratio);
            } else {
                $scaled_height = 400;
                $scaled_width = intval(400 * $aspect_ratio);
            }

            $resized = imagecreatetruecolor($scaled_width, $scaled_height);
            imagecopyresampled(
                $resized,
                $image_source,
                0,
                0,
                0,
                0,
                $scaled_width,
                $scaled_height,
                $width,
                $height
            );

            $blur_hash = \kornrunner\Blurhash\Blurhash::encode($resized, $components_x, $components_y);

            imagedestroy($image_source);
            imagedestroy($resized);

            return $blur_hash;
        } catch (\Exception $exception) {
            info($exception->getMessage());
            return null;
        }
    }

    public static function get_full_url($path, $data, $type, $placeholder = null)
    {
        $host = env('APP_URL', config('app.url'));

        // Handle case where $data is a string instead of an array
        if (is_string($data)) {
            $local = [
                'logo' => 'storage/restaurant/' . $data,
                'cover_photo' => 'storage/restaurant/' . $data,
                'image' => 'storage/product/' . $data,
            ];
        } else {
            $local = [
                'logo' => 'storage/restaurant/' . ($data['logo'] ?? ''),
                'cover_photo' => 'storage/restaurant/' . ($data['cover_photo'] ?? ''),
                'image' => 'storage/product/' . ($data['image'] ?? ''),
            ];
        }

        $host = str_replace('index.php', '', $host);

        if ($placeholder) {
            if (!strpos($placeholder, 'http')) {
                $placeholder = $host . $placeholder;
            }
        } else {
            $placeholder = $host . 'storage/app/public/restaurant/cover.png';
        }

        $place_holders = [
            'restaurant' => $host . 'public/assets/placeholder.png',
            'product' => $host . 'public/assets/placeholder.png',
            'category' => $host . 'public/assets/placeholder.png',
            'reviewer' => $host . 'public/assets/placeholder.png',
            'default' => $host . 'public/assets/placeholder.png',
        ];

        $type = data_get($data, 'type', $type);

        $data_type = data_get($data, 'storage', config('filesystems.default'));

        $place_holder = data_get($data, 'placeholder_image', null);

        if ($path == 'restaurant') {
            $place_holder = $place_holder ?? $place_holders['restaurant'];
        }

        if ($place_holder && !Str::startsWith($place_holder, ['http://', 'https://'])) {
            $place_holder = $host . $place_holder;
        }

        if ($data_type == 's3') {
            if ($data && Storage::disk('s3')->exists($path . '/' . $data)) {
                return Storage::disk('s3')->url($path . '/' . $data);
            }
        } else {
            if ($data && Storage::disk('public')->exists($path . '/' . $data)) {
                return dynamicStorage('storage/app/public') . '/' . $path . '/' . $data;
            }
        }

        return $place_holder;
    }

    public static function updateStorageRecord($dataType, $dataId, $image)
    {
        $value = self::getDisk();
        DB::table('storages')->updateOrInsert([
            'data_type' => $dataType,
            'data_id' => $dataId,
            'key' => 'image',
        ], [
            'value' => $value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return true;
    }
}
