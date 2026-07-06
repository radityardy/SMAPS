<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'specialization' => $this->specialization,
            'queue_prefix' => $this->queue_prefix,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}
