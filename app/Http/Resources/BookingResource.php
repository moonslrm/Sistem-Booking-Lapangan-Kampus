<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'booking_code' => $this->booking_code,
            'status' => $this->status,
            'booking_date' => $this->booking_date?->toDateString(),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'duration_hours' => (float) $this->duration_hours,
            'price_per_hour' => (float) $this->price_per_hour,
            'total_price' => (float) $this->total_price,
            'discount_amount' => (float) $this->discount_amount,
            'final_price' => (float) $this->final_price,
            'voucher_code' => $this->voucher_code,
            'is_campus_price' => $this->is_campus_price,
            'cancelled_at' => $this->cancelled_at?->toDateTimeString(),
            'cancellation_reason' => $this->cancellation_reason,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'venue' => $this->whenLoaded('venue', function () {
                return [
                    'id' => $this->venue->id,
                    'name' => $this->venue->name,
                    'sport_type' => $this->venue->sport_type,
                ];
            }),
            'slot' => $this->whenLoaded('slot', function () {
                return [
                    'id' => $this->slot->id,
                    'start_time' => $this->slot->start_time,
                    'end_time' => $this->slot->end_time,
                ];
            }),
        ];
    }
}
