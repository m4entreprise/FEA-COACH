<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientDocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'client_id' => $this->client_id,
            'title' => $this->title,
            'category' => $this->category,
            'category_label' => $this->getCategoryLabel(),
            'file_name' => $this->file_name,
            'file_size' => $this->file_size,
            'formatted_size' => $this->formatted_size,
            'mime_type' => $this->mime_type,
            'download_url' => $this->download_url,
            'uploaded_at' => $this->uploaded_at->format('d/m/Y H:i'),
        ];
    }

    private function getCategoryLabel(): string
    {
        return match($this->category) {
            'medical' => 'Médical',
            'program' => 'Programme',
            'nutrition' => 'Nutrition',
            'contract' => 'Contrat',
            'results' => 'Résultats',
            'other' => 'Autre',
            default => $this->category,
        };
    }
}
