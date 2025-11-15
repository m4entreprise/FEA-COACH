<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientAssessmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'client_id' => $this->client_id,
            'energy_level' => $this->energy_level,
            'difficulty_level' => $this->difficulty_level,
            'progress_notes' => $this->progress_notes,
            'coach_comments' => $this->coach_comments,
            'status' => $this->status,
            'status_label' => $this->status === 'completed' ? 'Complété' : 'En attente',
            'assessment_date' => $this->assessment_date->format('Y-m-d'),
            'assessment_date_formatted' => $this->assessment_date->format('d/m/Y'),
            'created_at' => $this->created_at->format('d/m/Y H:i'),
            'updated_at' => $this->updated_at->format('d/m/Y H:i'),
        ];
    }
}
