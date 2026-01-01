<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientShareController extends Controller
{
    public function show(Request $request, string $token)
    {
        $client = Client::with([
                'documents' => fn ($query) => $query->orderBy('type')->orderByDesc('version'),
                'notes' => fn ($query) => $query->orderByDesc('created_at'),
            ])
            ->where('share_token', $token)
            ->firstOrFail();

        $sessionKey = $this->sessionKey($client->id);
        $hasAccess = (bool) $request->session()->get($sessionKey, false);

        if (!$hasAccess) {
            return view('share.enter-code', [
                'client' => $client,
                'token' => $token,
            ]);
        }

        $documentsByType = $client->documents->groupBy('type');
        $types = config('client_documents.types', []);

        return view('share.documents', [
            'client' => $client,
            'token' => $token,
            'documentsByType' => $documentsByType,
            'types' => $types,
        ]);
    }

    public function unlock(Request $request, string $token)
    {
        $client = Client::where('share_token', $token)->firstOrFail();

        $data = $request->validate([
            'share_code' => ['required', 'string', 'size:6'],
        ]);

        if ($data['share_code'] !== $client->share_code) {
            return back()->withErrors([
                'share_code' => 'Code incorrect, veuillez réessayer.',
            ]);
        }

        $request->session()->put($this->sessionKey($client->id), true);

        return redirect()->route('clients.share.show', $token)->with('status', 'Code accepté.');
    }

    public function download(Request $request, string $token, ClientDocument $document)
    {
        $client = Client::where('share_token', $token)->firstOrFail();

        if ($document->client_id !== $client->id) {
            abort(404);
        }

        if (!$request->session()->get($this->sessionKey($client->id), false)) {
            return redirect()->route('clients.share.show', $token)
                ->withErrors(['share_code' => 'Veuillez saisir le code à 6 chiffres pour accéder aux documents.']);
        }

        $document->logs()->create([
            'action' => 'downloaded',
            'actor' => 'student',
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return Storage::disk('local')->download($document->storage_path, $document->filename);
    }

    protected function sessionKey(int $clientId): string
    {
        return 'client_share_access_' . $clientId;
    }
}
