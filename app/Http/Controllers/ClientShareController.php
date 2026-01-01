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
        $client = Client::with('coach.user')->where('share_token', $token)->firstOrFail();

        $sessionKey = $this->sessionKey($client->id);
        $hasAccess = (bool) $request->session()->get($sessionKey, false);

        if (!$hasAccess) {
            return view('share.enter-code', [
                'client' => $client,
                'token' => $token,
            ]);
        }

        return redirect()->route('clients.dashboard.program', $token);
    }

    public function program(Request $request, string $token)
    {
        $client = Client::with([
                'documents' => fn ($query) => $query->where('type', 'program')->orderByDesc('version'),
                'coach.user',
            ])
            ->where('share_token', $token)
            ->firstOrFail();

        if (!$request->session()->get($this->sessionKey($client->id), false)) {
            return redirect()->route('clients.share.show', $token);
        }

        $latestDocuments = $client->documents->unique(fn($doc) => $doc->id)->values();
        $olderVersions = $client->documents()->where('type', 'program')
            ->get()
            ->groupBy(fn($doc) => $doc->id)
            ->map(fn($group) => $group->skip(1));

        return view('client-dashboard.program', [
            'client' => $client,
            'documents' => $latestDocuments,
            'olderVersions' => $olderVersions,
            'programCount' => $client->documents()->where('type', 'program')->count(),
            'nutritionCount' => $client->documents()->where('type', 'nutrition')->count(),
            'assessmentCount' => $client->documents()->where('type', 'assessment')->count(),
            'notesCount' => $client->notes()->count(),
        ]);
    }

    public function nutrition(Request $request, string $token)
    {
        $client = Client::with([
                'documents' => fn ($query) => $query->where('type', 'nutrition')->orderByDesc('version'),
                'coach.user',
            ])
            ->where('share_token', $token)
            ->firstOrFail();

        if (!$request->session()->get($this->sessionKey($client->id), false)) {
            return redirect()->route('clients.share.show', $token);
        }

        $latestDocuments = $client->documents->unique(fn($doc) => $doc->id)->values();
        $olderVersions = $client->documents()->where('type', 'nutrition')
            ->get()
            ->groupBy(fn($doc) => $doc->id)
            ->map(fn($group) => $group->skip(1));

        return view('client-dashboard.nutrition', [
            'client' => $client,
            'documents' => $latestDocuments,
            'olderVersions' => $olderVersions,
            'programCount' => $client->documents()->where('type', 'program')->count(),
            'nutritionCount' => $client->documents()->where('type', 'nutrition')->count(),
            'assessmentCount' => $client->documents()->where('type', 'assessment')->count(),
            'notesCount' => $client->notes()->count(),
        ]);
    }

    public function assessment(Request $request, string $token)
    {
        $client = Client::with([
                'documents' => fn ($query) => $query->where('type', 'assessment')->orderByDesc('version'),
                'coach.user',
            ])
            ->where('share_token', $token)
            ->firstOrFail();

        if (!$request->session()->get($this->sessionKey($client->id), false)) {
            return redirect()->route('clients.share.show', $token);
        }

        $latestDocuments = $client->documents->unique(fn($doc) => $doc->id)->values();
        $olderVersions = $client->documents()->where('type', 'assessment')
            ->get()
            ->groupBy(fn($doc) => $doc->id)
            ->map(fn($group) => $group->skip(1));

        return view('client-dashboard.assessment', [
            'client' => $client,
            'documents' => $latestDocuments,
            'olderVersions' => $olderVersions,
            'programCount' => $client->documents()->where('type', 'program')->count(),
            'nutritionCount' => $client->documents()->where('type', 'nutrition')->count(),
            'assessmentCount' => $client->documents()->where('type', 'assessment')->count(),
            'notesCount' => $client->notes()->count(),
        ]);
    }

    public function notes(Request $request, string $token)
    {
        $client = Client::with([
                'notes' => fn ($query) => $query->orderByDesc('created_at'),
                'coach.user',
            ])
            ->where('share_token', $token)
            ->firstOrFail();

        if (!$request->session()->get($this->sessionKey($client->id), false)) {
            return redirect()->route('clients.share.show', $token);
        }

        return view('client-dashboard.notes', [
            'client' => $client,
            'notes' => $client->notes,
            'programCount' => $client->documents()->where('type', 'program')->count(),
            'nutritionCount' => $client->documents()->where('type', 'nutrition')->count(),
            'assessmentCount' => $client->documents()->where('type', 'assessment')->count(),
            'notesCount' => $client->notes()->count(),
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
