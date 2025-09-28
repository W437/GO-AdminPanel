<?php

namespace App\Http\Requests\Story;

use Illuminate\Foundation\Http\FormRequest;

class UploadStoryMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $mediaType = $this->input('media_type');

        $mediaRules = ['required', 'file'];
        $thumbRules = ['nullable', 'file', 'mimetypes:image/jpeg,image/png,image/webp', 'max:10240'];

        if ($mediaType === 'video') {
            $mediaRules[] = 'mimetypes:video/mp4,video/quicktime,video/3gpp,video/x-msvideo';
            $mediaRules[] = 'max:20480';
        } else {
            $mediaRules[] = 'mimetypes:image/jpeg,image/png,image/webp';
            $mediaRules[] = 'max:10240';
        }

        return [
            'media_type' => ['required', 'string', 'in:image,video'],
            'sequence' => ['nullable', 'integer', 'min:1', 'max:50'],
            'caption' => ['nullable', 'string', 'max:240'],
            'cta_label' => ['nullable', 'string', 'max:120'],
            'cta_url' => ['nullable', 'url'],
            'duration_seconds' => ['nullable', 'integer', 'min:1', 'max:60'],
            'media' => $mediaRules,
            'thumbnail' => $thumbRules,
        ];
    }
}
