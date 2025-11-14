<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoCodeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PromoCodeRequestController extends Controller
{
    /**
     * Afficher toutes les demandes de code promo
     */
    public function index()
    {
        $requests = PromoCodeRequest::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/PromoCodeRequests/Index', [
            'requests' => $requests,
        ]);
    }

    /**
     * Approuver une demande et générer un code promo
     */
    public function approve(Request $request, PromoCodeRequest $promoCodeRequest)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        // Générer un code promo unique
        $promoCode = 'FEA-' . strtoupper(Str::random(8));

        $promoCodeRequest->status = 'approved';
        $promoCodeRequest->promo_code = $promoCode;
        $promoCodeRequest->admin_notes = $request->admin_notes;
        $promoCodeRequest->save();

        // TODO: Envoyer un email à l'utilisateur avec le code promo

        return redirect()->back()->with('success', 'Demande approuvée. Code promo : ' . $promoCode);
    }

    /**
     * Rejeter une demande
     */
    public function reject(Request $request, PromoCodeRequest $promoCodeRequest)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        $promoCodeRequest->status = 'rejected';
        $promoCodeRequest->admin_notes = $request->admin_notes;
        $promoCodeRequest->save();

        // TODO: Envoyer un email à l'utilisateur

        return redirect()->back()->with('success', 'Demande rejetée.');
    }
}
