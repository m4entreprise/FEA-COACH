<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssessmentRequest;
use App\Http\Requests\UpdateAssessmentRequest;
use App\Models\Client;
use App\Models\ClientAssessment;
use App\Services\ClientActivityService;

class ClientAssessmentController extends Controller
{
    public function __construct(
        private ClientActivityService $activityService
    ) {}

    /**
     * Store a new assessment
     */
    public function store(StoreAssessmentRequest $request, Client $client)
    {
        $coach = auth()->user()->coach;

        if (!$coach || $client->coach_id !== $coach->id) {
            return back()->with('error', 'Accès non autorisé.');
        }

        $assessment = $client->assessments()->create($request->validated());

        // Log activity
        $this->activityService->logAssessmentCreated($client, $assessment->id, $request->validated());

        return back()->with('success', 'Bilan créé avec succès !');
    }

    /**
     * Update an assessment
     */
    public function update(UpdateAssessmentRequest $request, Client $client, ClientAssessment $assessment)
    {
        $coach = auth()->user()->coach;

        if (!$coach || $client->coach_id !== $coach->id || $assessment->client_id !== $client->id) {
            return back()->with('error', 'Accès non autorisé.');
        }

        $wasCompleted = $assessment->status === 'completed';
        
        $assessment->update($request->validated());

        // Log si le bilan vient d'être complété
        if (!$wasCompleted && $assessment->status === 'completed') {
            $this->activityService->logAssessmentCompleted($client, $assessment->id);
        }

        return back()->with('success', 'Bilan mis à jour avec succès !');
    }

    /**
     * Delete an assessment
     */
    public function destroy(Client $client, ClientAssessment $assessment)
    {
        $coach = auth()->user()->coach;

        if (!$coach || $client->coach_id !== $coach->id || $assessment->client_id !== $client->id) {
            return back()->with('error', 'Accès non autorisé.');
        }

        $assessment->delete();

        return back()->with('success', 'Bilan supprimé avec succès !');
    }

    /**
     * Mark assessment as completed
     */
    public function complete(Client $client, ClientAssessment $assessment)
    {
        $coach = auth()->user()->coach;

        if (!$coach || $client->coach_id !== $coach->id || $assessment->client_id !== $client->id) {
            return back()->with('error', 'Accès non autorisé.');
        }

        if ($assessment->status !== 'completed') {
            $assessment->markAsCompleted();
            $this->activityService->logAssessmentCompleted($client, $assessment->id);
        }

        return back()->with('success', 'Bilan marqué comme complet !');
    }
}
