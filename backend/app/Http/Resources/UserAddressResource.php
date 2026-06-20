<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'address_line' => $this->address_line,
            'subdistrict_id' => $this->subdistrict_id,
            'subdistrict_name' => $this->subdistrict_name,
            'district_id' => $this->district_id,
            'district_name' => $this->district_name,
            'regency_id' => $this->regency_id,
            'regency_name' => $this->regency_name,
            'province_id' => $this->province_id,
            'province_name' => $this->province_name,
            'postal_code' => $this->postal_code,
            'is_default' => $this->is_default,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
