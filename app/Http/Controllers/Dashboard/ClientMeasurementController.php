<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMeasurementRequest;
use App\Models\Client;
use App\Models\ClientMeasurement;
use App\Services\ClientActivityService;
use Illuminate\Support\Facades\Storage;

class ClientMeasurementController extends Controller
{
    public function __construct(
        private ClientActivityService $activityService
    ) {}

    /**
     * Store a new measurement
     */
    public function store(StoreMeasurementRequest $request, Client $client)
    {
        $coach = auth()->user()->coach;

        if (!$coach || $client->coach_id !== $coach->id) {
            return back()->with('error', 'Accès non autorisé.');
        }

        $validated = $request->validated();

        // Gérer l'upload des photos si présentes
        if ($request->hasFile('photos')) {
            $photoPaths = [];
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('measurements', 'public');
                $photoPaths[] = $path;
            }
            $validated['photos'] = $photoPaths;
        }

        $measurement = $client->measurements()->create($validated);

        // Log activity
        $this->activityService->logMeasurementAdded($client, $measurement->id, $validated);

        return back()->with('success', 'Mesure enregistrée avec succès !');
    }

    /**
     * Update a measurement
     */
    public function update(StoreMeasurementRequest $request, Client $client, ClientMeasurement $measurement)
    {
        $coach = auth()->user()->coach;

        if (!$coach || $client->coach_id !== $coach->id || $measurement->client_id !== $client->id) {
            return back()->with('error', 'Accès non autorisé.');
        }

        $validated = $request->validated();

        // Gérer l'upload des photos si présentes
        if ($request->hasFile('photos')) {
            // Supprimer les anciennes photos
            if ($measurement->photos) {
                foreach ($measurement->photos as $oldPhoto) {
                    Storage::disk('public')->delete($oldPhoto);
                }
            }

            $photoPaths = [];
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('measurements', 'public');
                $photoPaths[] = $path;
            }
            $validated['photos'] = $photoPaths;
        }

        $measurement->update($validated);

        return back()->with('success', 'Mesure mise à jour avec succès !');
    }

    /**
     * Delete a measurement
     */
    public function destroy(Client $client, ClientMeasurement $measurement)
    {
        $coach = auth()->user()->coach;

        if (!$coach || $client->coach_id !== $coach->id || $measurement->client_id !== $client->id) {
            return back()->with('error', 'Accès non autorisé.');
        }

        // Supprimer les photos associées
        if ($measurement->photos) {
            foreach ($measurement->photos as $photo) {
                Storage::disk('public')->delete($photo);
            }
        }

        $measurement->delete();

        return back()->with('success', 'Mesure supprimée avec succès !');
    }
}
