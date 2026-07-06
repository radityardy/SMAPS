<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QueueResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'doctor' => new DoctorResource($this->whenLoaded('doctor')),
            'patient_name' => $this->patient_name,
            'patient_phone' => $this->patient_phone,
            'complaint' => $this->complaint,
            'queue_number' => $this->queue_number,
            'status' => $this->status,
            'queue_date' => $this->queue_date?->toDateString(),
            'called_at' => $this->called_at,
            'served_at' => $this->served_at,
            'completed_at' => $this->completed_at,
            'created_at' => $this->created_at,
        ];
    }
}
