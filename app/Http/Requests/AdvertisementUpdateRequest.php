<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use App\CentralLogics\Helpers;
use Illuminate\Contracts\Validation\Validator;
class AdvertisementUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules()
    {
        return [
            'restaurant_id' => 'required|exists:restaurants,id',
            'title.*' => 'max:255',
            'title.0' => 'required|max:255',
            'description.*' => 'nullable|max:1000',
            'dates' => 'required',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'priority' => 'nullable|integer|min:0',
            'status' => 'nullable|in:approved,pending,denied',
        ];
    }

    public function messages()
    {
        return [
            'restaurant_id.required' => translate('messages.Please_select_a_restaurant'),
            'restaurant_id.exists' => translate('messages.restaurant_not_found'),
            'title.0.required' => translate('default_title_is_required'),
            'end_date.after' => translate('messages.End date must be after start date'),
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $dateRange = $this->dates;
            list($startDate, $endDate) = explode(' - ', $dateRange);
            $startDate = Carbon::createFromFormat('m/d/Y', trim($startDate))->startOfDay();
            $endDate = Carbon::createFromFormat('m/d/Y', trim($endDate))->endOfDay();

            if ($startDate < Carbon::today()) {
                $validator->errors()->add('date', translate('messages.Start date must be greater than or equal to today'));
            }

            if ($endDate < $startDate) {
                $validator->errors()->add('date', translate('messages.End date must be greater than start date'));
            }
        });
    }
    protected function failedValidation(Validator $validator)
    {
        $response = response()->json(['errors' => Helpers::error_processor($validator)]);
        throw new ValidationException($validator, $response);
    }

}
