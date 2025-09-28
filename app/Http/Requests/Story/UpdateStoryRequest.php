<?php

namespace App\Http\Requests\Story;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'nullable', 'string', 'max:120'],
            'publish_at' => ['sometimes', 'nullable', 'date', 'after_or_equal:now'],
            'publish_now' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'in:draft,scheduled,published'],
        ];
    }
}
