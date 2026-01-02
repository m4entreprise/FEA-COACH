<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\CustomDomain;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomDomainController extends Controller
{
    /**
     * Display a listing of custom domains.
     */
    public function index()
    {
        $domains = CustomDomain::with('coach.user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($domain) => [
                'id' => $domain->id,
                'domain' => $domain->domain,
                'status' => $domain->status,
                'coach_id' => $domain->coach_id,
                'coach_name' => $domain->coach?->name ?? 'Coach supprimé',
                'coach_email' => $domain->coach?->user?->email ?? 'N/A',
                'purchased_at' => $domain->purchased_at?->format('d/m/Y'),
                'expires_at' => $domain->expires_at?->format('d/m/Y'),
                'notes' => $domain->notes,
                'created_at' => $domain->created_at->format('d/m/Y H:i'),
            ]);

        $coaches = Coach::with('user')
            ->whereHas('user')
            ->orderBy('name')
            ->get()
            ->map(fn($coach) => [
                'id' => $coach->id,
                'name' => $coach->name,
                'email' => $coach->user->email ?? 'N/A',
            ]);

        // Pending custom domain requests (saisies dans la modale)
        $pendingRequests = Coach::with('user')
            ->whereHas('user')
            ->whereNotNull('desired_custom_domain')
            ->orderBy('name')
            ->get()
            ->map(fn($coach) => [
                'coach_id' => $coach->id,
                'coach_name' => $coach->name,
                'coach_email' => $coach->user->email ?? 'N/A',
                'desired_domain' => $coach->desired_custom_domain,
            ]);

        return Inertia::render('Admin/CustomDomains', [
            'domains' => $domains,
            'coaches' => $coaches,
            'pendingRequests' => $pendingRequests,
        ]);
    }

    /**
     * Store a newly created custom domain.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'coach_id' => 'required|exists:coaches,id',
            'domain' => 'required|string|max:255|unique:custom_domains,domain',
            'status' => 'required|in:pending,active,expired,cancelled',
            'purchased_at' => 'nullable|date',
            'expires_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $domain = CustomDomain::create($validated);

        // Clear pending desired domain once a real CustomDomain is created
        $coach = Coach::find($validated['coach_id']);
        if ($coach) {
            $coach->desired_custom_domain = null;
            $coach->save();
        }

        return redirect()->route('admin.custom-domains.index')
            ->with('success', 'Nom de domaine ajouté avec succès.');
    }

    /**
     * Update the specified custom domain.
     */
    public function update(Request $request, CustomDomain $customDomain)
    {
        $validated = $request->validate([
            'domain' => ['required', 'string', 'max:255', 'unique:custom_domains,domain,' . $customDomain->id],
            'status' => 'required|in:pending,active,expired,cancelled',
            'purchased_at' => 'nullable|date',
            'expires_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $customDomain->update($validated);

        return redirect()->route('admin.custom-domains.index')
            ->with('success', 'Nom de domaine mis à jour avec succès.');
    }

    /**
     * Remove the specified custom domain.
     */
    public function destroy(CustomDomain $customDomain)
    {
        $customDomain->delete();

        return redirect()->route('admin.custom-domains.index')
            ->with('success', 'Nom de domaine supprimé avec succès.');
    }
}
