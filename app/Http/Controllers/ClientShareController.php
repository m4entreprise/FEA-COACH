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

    public function profile(Request $request, string $token)
    {
        $client = Client::with(['coach.user', 'latestMeasurement'])
            ->where('share_token', $token)
            ->firstOrFail();

        if (!$request->session()->get($this->sessionKey($client->id), false)) {
            return redirect()->route('clients.share.show', $token);
        }

        return view('client-dashboard.profile-complete', [
            'client' => $client,
            'latestMeasurement' => $client->latestMeasurement,
            'programCount' => $client->documents()->where('type', 'program')->count(),
            'nutritionCount' => $client->documents()->where('type', 'nutrition')->count(),
            'assessmentCount' => $client->documents()->where('type', 'assessment')->count(),
            'notesCount' => $client->notes()->count(),
        ]);
    }

    public function updateProfile(Request $request, string $token)
    {
        $client = Client::where('share_token', $token)->firstOrFail();

        if (!$request->session()->get($this->sessionKey($client->id), false)) {
            return redirect()->route('clients.share.show', $token);
        }

        $validated = $request->validate([
            // Mesures physiques (pour historique)
            'weight' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'height' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'chest' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'waist' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'hips' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'arm' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'thigh' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'photo_front' => ['nullable', 'image', 'max:5120'],
            'photo_side' => ['nullable', 'image', 'max:5120'],
            'photo_back' => ['nullable', 'image', 'max:5120'],
            
            // Santé & Physiologie
            'injuries' => ['nullable', 'string', 'max:5000'],
            'stress_level' => ['nullable', 'integer', 'min:1', 'max:10'],
            'sleep_quality' => ['nullable', 'string', 'in:bad,average,good,excellent'],
            'menstrual_tracking' => ['nullable', 'boolean'],
            'last_period' => ['nullable', 'date'],
            
            // Nutrition & Cuisine
            'allergies' => ['nullable', 'string', 'max:5000'],
            'dislikes' => ['nullable', 'string', 'max:5000'],
            'grocery_budget' => ['nullable', 'string', 'in:eco,standard,premium'],
            'kitchen_equipment' => ['nullable', 'array'],
            'kitchen_equipment.*' => ['string'],
            'supplements' => ['nullable', 'string', 'max:1000'],
            
            // Contexte Sportif
            'available_equipment' => ['nullable', 'array'],
            'available_equipment.*' => ['string'],
            'training_frequency' => ['nullable', 'string', 'in:2x,3x,4x,5x+'],
            'session_duration' => ['nullable', 'string', 'in:30min,45min,1h,1h30'],
            'daily_activity' => ['nullable', 'string', 'in:sedentary,active,very_active'],
            
            // Psychologie & Profiling
            'main_goal' => ['nullable', 'string', 'max:5000'],
            'deep_motivation' => ['nullable', 'string', 'max:5000'],
            'general_comments' => ['nullable', 'string', 'max:10000'],
            'coaching_style' => ['nullable', 'string', 'in:strict,supportive,autonomous'],
        ]);

        // Séparer les données : métriques vs profil statique
        $measurementData = [];
        $hasMeasurements = false;
        
        foreach (['weight', 'height', 'chest', 'waist', 'hips', 'arm', 'thigh'] as $field) {
            if (!empty($validated[$field])) {
                $measurementData[$field] = $validated[$field];
                $hasMeasurements = true;
            }
        }

        // Upload sécurisé des photos
        foreach (['photo_front', 'photo_side', 'photo_back'] as $photoField) {
            if ($request->hasFile($photoField)) {
                $file = $request->file($photoField);
                $path = $file->store('client-photos/' . $client->id, 'local');
                $measurementData[$photoField] = $path;
                $hasMeasurements = true;
            }
        }

        // INSERT dans l'historique si au moins une métrique est renseignée
        if ($hasMeasurements) {
            $measurementData['client_id'] = $client->id;
            \App\Models\ClientMeasurement::create($measurementData);
        }

        // UPDATE du profil statique
        $profileData = array_diff_key($validated, array_flip([
            'weight', 'height', 'chest', 'waist', 'hips', 'arm', 'thigh',
            'photo_front', 'photo_side', 'photo_back'
        ]));
        
        $client->update($profileData);

        return redirect()->route('clients.dashboard.profile', $token)
            ->with('success', 'Votre fiche personnage a été mise à jour avec succès.');
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
