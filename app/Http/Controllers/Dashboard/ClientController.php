<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientNote;
use App\Models\ClientDocument;
use App\Models\ClientMessage;
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
            ])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $view = $request->boolean('beta')
            ? 'Coach/ClientsBeta'
            : 'Dashboard/Clients';

        return Inertia::render($view, [
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

        $redirectParams = $request->boolean('beta') ? ['beta' => 1] : [];

        return redirect()->route('dashboard.clients.index', $redirectParams)
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

        $redirectParams = $request->boolean('beta') ? ['beta' => 1] : [];

        return redirect()->route('dashboard.clients.index', $redirectParams)
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

        $redirectParams = request()->boolean('beta') ? ['beta' => 1] : [];

        return redirect()->route('dashboard.clients.index', $redirectParams)
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

    private function generateShareCode(): string
    {
        do {
            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (Client::where('share_code', $code)->exists());

        return $code;
    }
}
