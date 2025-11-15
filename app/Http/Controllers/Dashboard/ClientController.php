<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use App\Models\ClientNote;
use App\Services\ClientActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function __construct(
        private ClientActivityService $activityService
    ) {}

    /**
     * Display a listing of the clients.
     */
    public function index()
    {
        $coach = auth()->user()->coach;

        if (!$coach) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous devez avoir un profil coach pour accéder à cette page.');
        }

        $clients = $coach->clients()
            ->withCount(['measurements', 'documents', 'assessments'])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get()
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'full_name' => $client->full_name,
                    'first_name' => $client->first_name,
                    'last_name' => $client->last_name,
                    'email' => $client->email,
                    'phone' => $client->phone,
                    'status' => $client->status,
                    'age' => $client->age,
                    'measurements_count' => $client->measurements_count,
                    'documents_count' => $client->documents_count,
                    'assessments_count' => $client->assessments_count,
                    'created_at' => $client->created_at->format('d/m/Y'),
                ];
            });

        return Inertia::render('Dashboard/Clients/Index', [
            'clients' => $clients,
        ]);
    }

    /**
     * Display the specified client with all details.
     */
    public function show(Client $client)
    {
        $coach = auth()->user()->coach;

        if (!$coach || $client->coach_id !== $coach->id) {
            return redirect()->route('dashboard.clients.index')
                ->with('error', 'Accès non autorisé.');
        }

        $client->load([
            'measurements' => fn($q) => $q->latest('measurement_date')->take(10),
            'documents' => fn($q) => $q->latest('uploaded_at')->take(10),
            'assessments' => fn($q) => $q->latest('assessment_date')->take(10),
            'activities' => fn($q) => $q->latest()->take(20),
        ]);

        return Inertia::render('Dashboard/Clients/Show', [
            'client' => [
                'id' => $client->id,
                'first_name' => $client->first_name,
                'last_name' => $client->last_name,
                'full_name' => $client->full_name,
                'email' => $client->email,
                'phone' => $client->phone,
                'date_of_birth' => $client->date_of_birth?->format('Y-m-d'),
                'age' => $client->age,
                'objectives' => $client->objectives,
                'internal_notes' => $client->internal_notes,
                'status' => $client->status,
                'created_at' => $client->created_at->format('d/m/Y'),
                'measurements' => $client->measurements,
                'documents' => $client->documents,
                'assessments' => $client->assessments,
                'activities' => $client->activities,
            ],
        ]);
    }

    /**
     * Store a newly created client.
     */
    public function store(StoreClientRequest $request)
    {
        $coach = auth()->user()->coach;

        if (!$coach) {
            return back()->with('error', 'Profil coach non trouvé.');
        }

        $client = $coach->clients()->create($request->validated());

        // Log activity
        $this->activityService->logClientCreated($client);

        return redirect()->route('dashboard.clients.show', $client)
            ->with('success', 'Client ajouté avec succès !');
    }

    /**
     * Update the specified client.
     */
    public function update(UpdateClientRequest $request, Client $client)
    {
        $coach = auth()->user()->coach;

        if (!$coach || $client->coach_id !== $coach->id) {
            return back()->with('error', 'Accès non autorisé.');
        }

        $oldStatus = $client->status;
        $changes = $client->fill($request->validated())->getDirty();
        $client->save();

        // Log activity
        if (count($changes) > 0) {
            $this->activityService->logClientUpdated($client, $changes);
            
            if (isset($changes['status'])) {
                $this->activityService->logStatusChanged($client, $oldStatus, $client->status);
            }
        }

        return back()->with('success', 'Client modifié avec succès !');
    }

    /**
     * Remove the specified client.
     */
    public function destroy(Client $client)
    {
        $coach = auth()->user()->coach;

        if (!$coach || $client->coach_id !== $coach->id) {
            return back()->with('error', 'Accès non autorisé.');
        }

        $client->delete();

        return redirect()->route('dashboard.clients.index')
            ->with('success', 'Client supprimé avec succès !');
    }

    /**
     * Store a new note for the client.
     */
    public function storeNote(Request $request, Client $client)
    {
        $coach = auth()->user()->coach;

        if (!$coach || $client->coach_id !== $coach->id) {
            return back()->with('error', 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        $client->notes()->create($validated);

        return back()->with('success', 'Note ajoutée avec succès !');
    }

    /**
     * Update the specified note.
     */
    public function updateNote(Request $request, ClientNote $note)
    {
        $coach = auth()->user()->coach;

        if (!$coach || $note->client->coach_id !== $coach->id) {
            return back()->with('error', 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        $note->update($validated);

        return back()->with('success', 'Note modifiée avec succès !');
    }

    /**
     * Remove the specified note.
     */
    public function destroyNote(ClientNote $note)
    {
        $coach = auth()->user()->coach;

        if (!$coach || $note->client->coach_id !== $coach->id) {
            return back()->with('error', 'Accès non autorisé.');
        }

        $note->delete();

        return back()->with('success', 'Note supprimée avec succès !');
    }
}
