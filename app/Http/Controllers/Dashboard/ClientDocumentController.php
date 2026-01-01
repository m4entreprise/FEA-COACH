<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClientDocumentController extends Controller
{
    public function store(Request $request, Client $client)
    {
        $coach = $request->user()->coach;

        if (!$coach || $client->coach_id !== $coach->id) {
            abort(403, 'Accès non autorisé.');
        }

        $types = array_keys(config('client_documents.types', []));

        $validated = $request->validate([
            'type' => ['required', 'string', 'in:' . implode(',', $types)],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'document' => ['required', 'file', 'max:10240'],
        ]);

        $file = $request->file('document');
        $type = $validated['type'];
        $nextVersion = ($client->documents()->where('type', $type)->max('version') ?? 0) + 1;

        $path = $file->store("clients/{$client->id}/{$type}", 'local');

        $document = $client->documents()->create([
            'type' => $type,
            'version' => $nextVersion,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'filesize' => $file->getSize(),
            'storage_path' => $path,
            'file_uuid' => (string) Str::uuid(),
        ]);

        $document->logs()->create([
            'action' => 'uploaded',
            'actor' => 'coach',
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Document ajouté avec succès.');
    }

    public function download(Request $request, ClientDocument $document)
    {
        $coach = $request->user()->coach;

        if (!$coach || $document->client->coach_id !== $coach->id) {
            abort(403, 'Accès non autorisé.');
        }

        $document->logs()->create([
            'action' => 'downloaded',
            'actor' => 'coach',
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return Storage::disk('local')->download($document->storage_path, $document->filename);
    }
}
