<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ClientMeasurementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'client_id' => $this->client_id,
            'weight' => $this->weight,
            'body_measurements' => $this->body_measurements,
            'body_fat_percentage' => $this->body_fat_percentage,
            'photos' => $this->photos ? array_map(fn($path) => Storage::url($path), $this->photos) : null,
            'measurement_date' => $this->measurement_date->format('Y-m-d'),
            'measurement_date_formatted' => $this->measurement_date->format('d/m/Y'),
            'notes' => $this->notes,
            'created_at' => $this->created_at->format('d/m/Y H:i'),
        ];
    }
}
