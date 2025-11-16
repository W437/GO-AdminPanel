<?php

namespace App\Http\Requests\Story;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $overlays = $this->input('overlays');

        if (is_string($overlays)) {
            $decoded = json_decode($overlays, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $this->merge(['overlays' => $decoded]);
            }
        }
    }

    public function rules(): array
    {
        return array_merge([
            'title' => ['sometimes', 'nullable', 'string', 'max:120'],
            'publish_at' => ['sometimes', 'nullable', 'date', 'after_or_equal:now'],
            'publish_now' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'in:draft,scheduled,published'],
            'type' => ['sometimes', 'nullable', 'string', 'in:image,video'],
            'media_url' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'thumbnail_url' => [
                'sometimes',
                'nullable',
                'url',
                'max:2048',
                Rule::requiredIf(fn () => $this->input('type') === 'video'),
            ],
            'duration_seconds' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:60'],
        ], $this->overlayRules(true));
    }

    protected function overlayRules(bool $forUpdate = false): array
    {
        $overlaysRule = $forUpdate
            ? ['sometimes', 'nullable', 'array', 'max:10']
            : ['nullable', 'array', 'max:10'];

        return [
            'overlays' => $overlaysRule,
            'overlays.*' => ['array'],
            'overlays.*.id' => ['nullable', 'string', 'max:191'],
            'overlays.*.text' => ['required', 'string', 'max:500'],
            'overlays.*.position' => ['nullable', 'array'],
            'overlays.*.position.x' => ['nullable', 'numeric', 'between:0,1'],
            'overlays.*.position.y' => ['nullable', 'numeric', 'between:0,1'],
            'overlays.*.scale' => ['nullable', 'numeric', 'between:0.1,10'],
            'overlays.*.rotation' => ['nullable', 'numeric', 'between:-360,360'],
            'overlays.*.fontFamily' => ['nullable', 'string', 'max:120'],
            'overlays.*.stylePreset' => ['nullable', 'string', 'max:120'],
            'overlays.*.color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6,8}$/'],
            'overlays.*.backgroundColor' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6,8}$/'],
            'overlays.*.backgroundMode' => ['nullable', 'string', 'max:60'],
            'overlays.*.alignment' => ['nullable', 'string', 'in:left,center,right'],
            'overlays.*.zIndex' => ['nullable', 'integer', 'between:0,100'],
        ];
    }
}
