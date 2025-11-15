<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'age' => $this->age,
            'objectives' => $this->objectives,
            'internal_notes' => $this->internal_notes,
            'status' => $this->status,
            'created_at' => $this->created_at->format('d/m/Y H:i'),
            'updated_at' => $this->updated_at->format('d/m/Y H:i'),
            
            // Compteurs si chargés
            'measurements_count' => $this->when(isset($this->measurements_count), $this->measurements_count),
            'documents_count' => $this->when(isset($this->documents_count), $this->documents_count),
            'assessments_count' => $this->when(isset($this->assessments_count), $this->assessments_count),
            
            // Relations si chargées
            'measurements' => ClientMeasurementResource::collection($this->whenLoaded('measurements')),
            'documents' => ClientDocumentResource::collection($this->whenLoaded('documents')),
            'assessments' => ClientAssessmentResource::collection($this->whenLoaded('assessments')),
            'activities' => ClientActivityResource::collection($this->whenLoaded('activities')),
        ];
    }
}
