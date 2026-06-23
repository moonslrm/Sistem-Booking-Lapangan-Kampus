<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QRValidationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'booking' => [
                'id' => $this->id,
                'booking_code' => $this->booking_code,
                'status' => $this->status,
                'booking_date' => $this->booking_date?->toDateString(),
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'venue' => [
                    'id' => $this->venue->id,
                    'name' => $this->venue->name,
                    'sport_type' => $this->venue->sport_type,
                ],
                'user' => [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'phone' => $this->user->phone,
                    'email' => $this->user->email,
                ],
                'slot' => [
                    'start_time' => $this->start_time,
                    'end_time' => $this->end_time,
                ],
            ],
        ];
    }
}
