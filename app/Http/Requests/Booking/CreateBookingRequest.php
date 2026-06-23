<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'venue_id' => ['required', 'integer', 'exists:venues,id'],
            'slot_id' => ['required', 'integer', 'exists:venue_slots,id'],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'voucher_code' => ['sometimes', 'nullable', 'string', 'exists:vouchers,code'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
