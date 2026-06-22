<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class VenuePhotoResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'url' => Storage::disk('public')->url($this->photo_path),
            'sort_order' => $this->sort_order,
        ];
    }
}
