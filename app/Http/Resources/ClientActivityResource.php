<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'client_id' => $this->client_id,
            'type' => $this->type,
            'description' => $this->description,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at->format('d/m/Y H:i'),
            'created_at_diff' => $this->created_at->diffForHumans(),
        ];
    }
}
