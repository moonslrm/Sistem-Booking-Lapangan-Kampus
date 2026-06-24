<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'discount_type' => $this->discount_type,
            'discount_value' => (float) $this->discount_value,
            'min_booking_amount' => (float) $this->min_booking_amount,
            'max_discount_amount' => $this->max_discount_amount ? (float) $this->max_discount_amount : null,
            'max_total_usage' => (int) $this->max_total_usage,
            'max_per_user' => (int) $this->max_per_user,
            'valid_from' => $this->valid_from,
            'valid_until' => $this->valid_until,
            'target_role' => $this->target_role,
            'is_active' => (bool) $this->is_active,
        ];
    }
}
