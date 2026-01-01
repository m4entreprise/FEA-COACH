<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientNote;
use App\Models\ClientDocument;
use App\Models\ClientMessage;
use App\Models\ClientMeasurement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ClientController extends Controller
{
    /**
     * Display a listing of the clients.
     */
    public function index(Request $request)
    {
        $coach = $request->user()->coach;

        if (!$coach) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous devez avoir un profil coach pour accéder à cette page.');
        }

        $clients = $coach->clients()
            ->with([
                'notes',
                'documents' => fn ($query) => $query->orderBy('type')->orderByDesc('version'),
                'messages' => fn ($query) => $query->orderBy('created_at', 'desc'),
                'measurements' => fn ($query) => $query->orderBy('created_at', 'desc'),
            ])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return Inertia::render('Coach/ClientsBeta', [
            'clients' => $clients,
            'documentTypes' => config('client_documents.types'),
            'shareBaseUrl' => url('/p'),
        ]);
    }

    /**
     * Display client management dashboard (mirror of client view).
     */
    public function manage(Request $request, Client $client)
    {
        $coach = $request->user()->coach;

        if (!$coach || $client->coach_id !== $coach->id) {
            return redirect()->route('dashboard.clients.index')
                ->with('error', 'Accès non autorisé.');
        }

        $client->load([
            'measurements' => fn($q) => $q->orderBy('created_at', 'desc'),
            'messages' => fn($q) => $q->orderBy('created_at'),
            'notes' => fn($q) => $q->orderBy('created_at', 'desc'),
            'documents' => fn($q) => $q->orderBy('type')->orderByDesc('version'),
        ]);

        return Inertia::render('Coach/ManageClient', [
            'client' => $client,
            'documentTypes' => config('client_documents.types'),
        ]);
    }

    /**
     * Grant coach direct access to client dashboard.
     */
    public function accessDashboard(Request $request, Client $client)
    {
        $coach = $request->user()->coach;

        if (!$coach || $client->coach_id !== $coach->id) {
            return back()->with('error', 'Accès non autorisé.');
        }

        // Store client access in session (same logic as ClientShareController::unlock)
        $sessionKey = 'client_access_' . $client->id;
        $request->session()->put($sessionKey, true);

        return back();
    }

    /**
     * Store a newly created client.
     */
    public function store(Request $request)
    {
        $coach = auth()->user()->coach;

        if (!$coach) {
            return back()->with('error', 'Profil coach non trouvé.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:1000',
            'vat_number' => 'nullable|string|max:255',
        ]);

        $client = $coach->clients()->create(array_merge($validated, [
            'share_code' => $this->generateShareCode(),
            'share_token' => (string) Str::uuid(),
        ]));

        return redirect()->route('dashboard.clients.index')
            ->with('success', 'Client ajouté avec succès !');
    }

    /**
     * Update the specified client.
     */
    public function update(Request $request, Client $client)
    {
        $coach = auth()->user()->coach;

        if (!$coach || $client->coach_id !== $coach->id) {
            return back()->with('error', 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:1000',
            'vat_number' => 'nullable|string|max:255',
        ]);

        $client->update($validated);

        return redirect()->route('dashboard.clients.index')
            ->with('success', 'Client modifié avec succès !');
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

    /**
     * Send a message to the client (coach side).
     */
    public function sendMessage(Request $request, Client $client)
    {
        $coach = auth()->user()->coach;

        if (!$coach || $client->coach_id !== $coach->id) {
            return back()->with('error', 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ]);

        $messageData = [
            'client_id' => $client->id,
            'sender_type' => 'coach',
            'message' => $validated['message'],
        ];

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store("message-attachments/{$client->id}", 'local');

            $messageData['attachment_path'] = $path;
            $messageData['attachment_name'] = $file->getClientOriginalName();
            $messageData['attachment_mime'] = $file->getMimeType();
            $messageData['attachment_size'] = $file->getSize();
        }

        ClientMessage::create($messageData);

        return back()->with('success', 'Message envoyé avec succès.');
    }

    /**
     * Mark client messages as read (coach side).
     */
    public function markMessagesAsRead(Request $request, Client $client)
    {
        $coach = auth()->user()->coach;

        if (!$coach || $client->coach_id !== $coach->id) {
            return back()->with('error', 'Accès non autorisé.');
        }

        $client->messages()
            ->where('sender_type', 'client')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back();
    }

    /**
     * Download message attachment (coach side).
     */
    public function downloadMessageAttachment(Request $request, Client $client, ClientMessage $message)
    {
        $coach = auth()->user()->coach;

        if (!$coach || $client->coach_id !== $coach->id || $message->client_id !== $client->id) {
            return back()->with('error', 'Accès non autorisé.');
        }

        if (!$message->hasAttachment()) {
            return back()->with('error', 'Ce message n\'a pas de pièce jointe.');
        }

        if (!Storage::disk('local')->exists($message->attachment_path)) {
            return back()->with('error', 'Le fichier n\'existe plus.');
        }

        return Storage::disk('local')->download(
            $message->attachment_path,
            $message->attachment_name
        );
    }

    /**
     * Store a new measurement for the client (coach side).
     */
    public function storeMeasurement(Request $request, Client $client)
    {
        $coach = auth()->user()->coach;

        if (!$coach || $client->coach_id !== $coach->id) {
            return back()->with('error', 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'weight' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'height' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'chest' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'waist' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'hips' => ['nullable', 'numeric', 'min:0', 'max:300'],
        ]);

        $measurementData = $validated;
        
        if (isset($validated['weight']) && isset($validated['height']) && $validated['weight'] > 0 && $validated['height'] > 0) {
            $heightInMeters = $validated['height'] / 100;
            $measurementData['bmi'] = round($validated['weight'] / ($heightInMeters * $heightInMeters), 2);
        }

        // Check if there's already a measurement this week
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        
        $existingMeasurement = $client->measurements()
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->first();

        if ($existingMeasurement) {
            // Update existing measurement with new values (only non-null values)
            $updateData = array_filter($measurementData, function($value) {
                return !is_null($value);
            });
            $existingMeasurement->update($updateData);
            return back()->with('success', 'Relevé de cette semaine mis à jour avec succès.');
        } else {
            // Create new measurement
            $client->measurements()->create($measurementData);
            return back()->with('success', 'Relevé ajouté avec succès.');
        }
    }

    /**
     * Update a measurement (coach side).
     */
    public function updateMeasurement(Request $request, Client $client, ClientMeasurement $measurement)
    {
        $coach = auth()->user()->coach;

        if (!$coach || $client->coach_id !== $coach->id || $measurement->client_id !== $client->id) {
            return back()->with('error', 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'weight' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'height' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'chest' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'waist' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'hips' => ['nullable', 'numeric', 'min:0', 'max:300'],
        ]);

        $measurementData = $validated;
        
        if (isset($validated['weight']) && isset($validated['height']) && $validated['weight'] > 0 && $validated['height'] > 0) {
            $heightInMeters = $validated['height'] / 100;
            $measurementData['bmi'] = round($validated['weight'] / ($heightInMeters * $heightInMeters), 2);
        }

        $measurement->update($measurementData);

        return back()->with('success', 'Relevé modifié avec succès.');
    }

    /**
     * Delete a measurement (coach side).
     */
    public function destroyMeasurement(Client $client, ClientMeasurement $measurement)
    {
        $coach = auth()->user()->coach;

        if (!$coach || $client->coach_id !== $coach->id || $measurement->client_id !== $client->id) {
            return back()->with('error', 'Accès non autorisé.');
        }

        $measurement->delete();

        return back()->with('success', 'Relevé supprimé avec succès.');
    }

    private function generateShareCode(): string
    {
        do {
            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (Client::where('share_code', $code)->exists());

        return $code;
    }
}
