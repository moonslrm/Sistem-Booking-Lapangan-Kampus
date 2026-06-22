<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VenueResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sport_type' => $this->sport_type,
            'description' => $this->description,
            'location' => $this->location,
            'facilities' => $this->facilities ?? [],
            'photos' => VenuePhotoResource::collection($this->whenLoaded('photos')),
            'slots' => $this->whenLoaded('slots', fn () => $this->slots->map(function ($slot) {
                return [
                    'id' => $slot->id,
                    'day_of_week' => $slot->day_of_week,
                    'start_time' => $slot->start_time,
                    'end_time' => $slot->end_time,
                    'price_normal' => $slot->price_normal,
                    'price_campus' => $slot->price_campus,
                    'is_active' => $slot->is_active,
                ];
            })),
            'is_active' => $this->is_active,
            'average_rating' => $this->reviews()->avg('rating') ? round($this->reviews()->avg('rating'), 2) : null,
            'total_reviews' => $this->reviews()->count(),
            'manager' => $this->when($this->relationLoaded('manager') && $this->manager, fn () => [
                'id' => $this->manager->id,
                'name' => $this->manager->name,
            ]),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
