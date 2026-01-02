<?php

namespace App\Http\Controllers\Dashboard;

use App\DataTransferObjects\LegalData;
use App\Http\Controllers\Controller;
use App\Services\LegalContentGenerator;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LegalController extends Controller
{
    public function __construct(
        private LegalContentGenerator $generator
    ) {}

    /**
     * Show the legal generator form.
     */
    public function edit(Request $request)
    {
        $coach = auth()->user()->coach;

        if (!$coach) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous devez avoir un profil coach pour accéder à cette page.');
        }

        $coach->load('user');

        return Inertia::render('Dashboard/LegalGenerator', [
            'coach' => $coach,
            'user' => auth()->user(),
        ]);
    }

    /**
     * Update the legal settings and regenerate content.
     */
    public function update(Request $request)
    {
        $coach = auth()->user()->coach;

        if (!$coach) {
            return back()->with('error', 'Profil coach non trouvé.');
        }

        $validated = $request->validate([
            // User entity fields
            'entity_type' => 'required|in:PP,SOC',
            'legal_name' => 'required|string|max:255',
            'company_number' => 'required|string|max:255',
            'legal_representative' => 'nullable|string|max:255',
            'phone_contact' => 'nullable|string|max:50',
            'vat_number' => 'nullable|string|max:255',
            'legal_address' => 'required|string|max:1000',
            
            // Coach service flags
            'is_coaching_presentiel' => 'boolean',
            'is_coaching_online' => 'boolean',
            'has_digital_products' => 'boolean',
            'has_subscriptions' => 'boolean',
            'use_client_photos' => 'boolean',
            
            // Business rules
            'vat_regime' => 'required|in:ASSUJETTI,FRANCHISE',
            'cancellation_delay' => 'required|integer|min:0',
            'tribunal_city' => 'required|string|max:255',
            'insurance_company' => 'nullable|string|max:255',
            'insurance_policy_number' => 'nullable|string|max:255',
        ]);

        // Update user fields
        auth()->user()->update([
            'entity_type' => $validated['entity_type'],
            'legal_name' => $validated['legal_name'],
            'company_number' => $validated['company_number'],
            'legal_representative' => $validated['legal_representative'] ?? null,
            'phone_contact' => $validated['phone_contact'] ?? null,
            'vat_number' => $validated['vat_number'] ?? null,
            'legal_address' => $validated['legal_address'],
        ]);

        // Update coach fields
        $coach->update([
            'is_coaching_presentiel' => $validated['is_coaching_presentiel'] ?? false,
            'is_coaching_online' => $validated['is_coaching_online'] ?? false,
            'has_digital_products' => $validated['has_digital_products'] ?? false,
            'has_subscriptions' => $validated['has_subscriptions'] ?? false,
            'use_client_photos' => $validated['use_client_photos'] ?? false,
            'vat_regime' => $validated['vat_regime'],
            'cancellation_delay' => $validated['cancellation_delay'],
            'tribunal_city' => $validated['tribunal_city'],
            'insurance_company' => $validated['insurance_company'] ?? null,
            'insurance_policy_number' => $validated['insurance_policy_number'] ?? null,
        ]);

        // Generate and save legal content if in AUTO mode
        if ($coach->legal_generation_mode === 'AUTO') {
            $coach->refresh();
            $coach->load('user');
            $html = $this->generator->generate($coach);
            $coach->update(['legal_terms' => $html]);
        }

        return back()->with('success', 'Mentions légales enregistrées avec succès !');
    }

    /**
     * Generate preview without saving.
     */
    public function generatePreview(Request $request)
    {
        $validated = $request->validate([
            'entity_type' => 'required|in:PP,SOC',
            'legal_name' => 'nullable|string|max:255',
            'company_number' => 'nullable|string|max:255',
            'legal_representative' => 'nullable|string|max:255',
            'phone_contact' => 'nullable|string|max:50',
            'vat_number' => 'nullable|string|max:255',
            'legal_address' => 'nullable|string|max:1000',
            'nom_commercial' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'is_coaching_presentiel' => 'boolean',
            'is_coaching_online' => 'boolean',
            'has_digital_products' => 'boolean',
            'has_subscriptions' => 'boolean',
            'use_client_photos' => 'boolean',
            'vat_regime' => 'required|in:ASSUJETTI,FRANCHISE',
            'cancellation_delay' => 'required|integer|min:0',
            'tribunal_city' => 'required|string|max:255',
            'insurance_company' => 'nullable|string|max:255',
            'insurance_policy_number' => 'nullable|string|max:255',
        ]);

        $legalData = LegalData::fromArray($validated);
        $html = $this->generator->generate($legalData);

        return response()->json(['html' => $html]);
    }
}
