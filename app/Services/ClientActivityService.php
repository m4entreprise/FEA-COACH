<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientActivity;

class ClientActivityService
{
    /**
     * Log une activité pour un client
     */
    public function log(Client $client, string $type, ?array $metadata = null, ?string $description = null): ClientActivity
    {
        return $client->activities()->create([
            'type' => $type,
            'metadata' => $metadata,
            'description' => $description ?? $this->generateDescription($type, $metadata),
        ]);
    }

    /**
     * Log la création d'un client
     */
    public function logClientCreated(Client $client): ClientActivity
    {
        return $this->log(
            $client,
            'client_created',
            ['client_id' => $client->id],
            "Client {$client->full_name} créé"
        );
    }

    /**
     * Log la mise à jour d'un client
     */
    public function logClientUpdated(Client $client, array $changes = []): ClientActivity
    {
        return $this->log(
            $client,
            'client_updated',
            ['changes' => $changes],
            "Informations du client mises à jour"
        );
    }

    /**
     * Log l'ajout d'une mesure
     */
    public function logMeasurementAdded(Client $client, int $measurementId, array $data = []): ClientActivity
    {
        return $this->log(
            $client,
            'measurement_added',
            [
                'measurement_id' => $measurementId,
                'weight' => $data['weight'] ?? null,
                'date' => $data['measurement_date'] ?? null,
            ],
            "Nouvelle mesure enregistrée" . (isset($data['weight']) ? " (Poids: {$data['weight']} kg)" : "")
        );
    }

    /**
     * Log le téléchargement d'un document
     */
    public function logDocumentUploaded(Client $client, int $documentId, string $title, string $category): ClientActivity
    {
        return $this->log(
            $client,
            'document_uploaded',
            [
                'document_id' => $documentId,
                'title' => $title,
                'category' => $category,
            ],
            "Document \"{$title}\" ajouté ({$this->getCategoryLabel($category)})"
        );
    }

    /**
     * Log la suppression d'un document
     */
    public function logDocumentDeleted(Client $client, string $title): ClientActivity
    {
        return $this->log(
            $client,
            'document_deleted',
            ['title' => $title],
            "Document \"{$title}\" supprimé"
        );
    }

    /**
     * Log la création d'un bilan
     */
    public function logAssessmentCreated(Client $client, int $assessmentId, array $data = []): ClientActivity
    {
        return $this->log(
            $client,
            'assessment_created',
            [
                'assessment_id' => $assessmentId,
                'date' => $data['assessment_date'] ?? null,
            ],
            "Nouveau bilan créé"
        );
    }

    /**
     * Log la complétion d'un bilan
     */
    public function logAssessmentCompleted(Client $client, int $assessmentId): ClientActivity
    {
        return $this->log(
            $client,
            'assessment_completed',
            ['assessment_id' => $assessmentId],
            "Bilan marqué comme complet"
        );
    }

    /**
     * Log le changement de statut d'un client
     */
    public function logStatusChanged(Client $client, string $oldStatus, string $newStatus): ClientActivity
    {
        return $this->log(
            $client,
            'status_changed',
            [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ],
            "Statut changé de \"{$this->getStatusLabel($oldStatus)}\" à \"{$this->getStatusLabel($newStatus)}\""
        );
    }

    /**
     * Génère une description par défaut selon le type d'activité
     */
    private function generateDescription(string $type, ?array $metadata = null): string
    {
        return match ($type) {
            'client_created' => 'Client créé',
            'client_updated' => 'Informations mises à jour',
            'measurement_added' => 'Mesure ajoutée',
            'document_uploaded' => 'Document ajouté',
            'document_deleted' => 'Document supprimé',
            'assessment_created' => 'Bilan créé',
            'assessment_completed' => 'Bilan complété',
            'status_changed' => 'Statut modifié',
            default => 'Activité enregistrée',
        };
    }

    /**
     * Retourne le label français d'une catégorie
     */
    private function getCategoryLabel(string $category): string
    {
        return match ($category) {
            'medical' => 'Médical',
            'program' => 'Programme',
            'nutrition' => 'Nutrition',
            'contract' => 'Contrat',
            'results' => 'Résultats',
            'other' => 'Autre',
            default => $category,
        };
    }

    /**
     * Retourne le label français d'un statut
     */
    private function getStatusLabel(string $status): string
    {
        return match ($status) {
            'active' => 'Actif',
            'inactive' => 'Inactif',
            'paused' => 'En pause',
            default => $status,
        };
    }
}
