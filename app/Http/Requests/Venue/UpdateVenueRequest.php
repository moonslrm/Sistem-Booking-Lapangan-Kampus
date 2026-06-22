<?php

namespace App\Http\Requests\Venue;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVenueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'sport_type' => ['sometimes', 'required', 'in:futsal,badminton,basket,voli,tenis'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string'],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['string'],
            'managed_by' => ['nullable', 'exists:users,id'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
