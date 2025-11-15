<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Models\Client;
use App\Models\ClientDocument;
use App\Services\ClientActivityService;
use Illuminate\Support\Facades\Storage;

class ClientDocumentController extends Controller
{
    public function __construct(
        private ClientActivityService $activityService
    ) {}

    /**
     * Store a new document
     */
    public function store(StoreDocumentRequest $request, Client $client)
    {
        $coach = auth()->user()->coach;

        if (!$coach || $client->coach_id !== $coach->id) {
            return back()->with('error', 'Accès non autorisé.');
        }

        $file = $request->file('file');
        $path = $file->store('client-documents', 'private');

        $document = $client->documents()->create([
            'title' => $request->title,
            'category' => $request->category,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'uploaded_at' => now(),
        ]);

        // Log activity
        $this->activityService->logDocumentUploaded($client, $document->id, $request->title, $request->category);

        return back()->with('success', 'Document ajouté avec succès !');
    }

    /**
     * Download a document
     */
    public function download(Client $client, ClientDocument $document)
    {
        $coach = auth()->user()->coach;

        if (!$coach || $client->coach_id !== $coach->id || $document->client_id !== $client->id) {
            abort(403, 'Accès non autorisé.');
        }

        if (!Storage::disk('private')->exists($document->file_path)) {
            abort(404, 'Fichier non trouvé.');
        }

        return Storage::disk('private')->download($document->file_path, $document->file_name);
    }

    /**
     * Delete a document
     */
    public function destroy(Client $client, ClientDocument $document)
    {
        $coach = auth()->user()->coach;

        if (!$coach || $client->coach_id !== $coach->id || $document->client_id !== $client->id) {
            return back()->with('error', 'Accès non autorisé.');
        }

        $title = $document->title;

        // Le hook boot() du modèle supprimera automatiquement le fichier
        $document->delete();

        // Log activity
        $this->activityService->logDocumentDeleted($client, $title);

        return back()->with('success', 'Document supprimé avec succès !');
    }
}
