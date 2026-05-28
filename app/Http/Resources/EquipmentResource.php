<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category,
            // Automatically uses your custom Cast!
            'formatted_rate' => $this->daily_rate, 
            'is_available' => $this->status === 'available',
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
