<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactController extends Controller
{
    /**
     * Display a listing of the coach's contact messages.
     */
    public function index(Request $request)
    {
        $coach = $request->user()->coach;

        if (! $coach) {
            return redirect()->route('dashboard')
                ->with('error', 'Aucun profil coach associé.');
        }

        $messages = ContactMessage::where('coach_id', $coach->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($message) => [
                'id' => $message->id,
                'name' => $message->name,
                'email' => $message->email,
                'phone' => $message->phone,
                'message' => $message->message,
                'is_read' => $message->is_read,
                'created_at' => $message->created_at->toDateTimeString(),
            ]);

        return Inertia::render('Coach/ContactBeta', [
            'messages' => $messages,
        ]);
    }

    /**
     * Mark the specified contact message as read.
     */
    public function markAsRead(Request $request, ContactMessage $contactMessage)
    {
        $coach = $request->user()->coach;

        if (! $coach || $contactMessage->coach_id !== $coach->id) {
            abort(403, 'Accès non autorisé.');
        }

        $contactMessage->update([
            'is_read' => true,
        ]);

        return redirect()->route('dashboard.contact')
            ->with('success', 'Message marqué comme lu.');
    }

    /**
     * Remove the specified contact message.
     */
    public function destroy(Request $request, ContactMessage $contactMessage)
    {
        $coach = $request->user()->coach;

        if (! $coach || $contactMessage->coach_id !== $coach->id) {
            abort(403, 'Accès non autorisé.');
        }

        $contactMessage->delete();

        return redirect()->route('dashboard.contact')
            ->with('success', 'Message supprimé avec succès.');
    }

    /**
     * Request contact for custom services (domain, custom website, etc.)
     */
    public function requestCustomService(Request $request)
    {
        $user = $request->user();
        $coach = $user->coach;

        if (! $coach) {
            return redirect()->route('dashboard')
                ->with('error', 'Aucun profil coach associé.');
        }

        // Empêche les demandes trop fréquentes : une demande tous les 7 jours
        if ($coach->custom_contact_locked_until && now()->lessThan($coach->custom_contact_locked_until)) {
            return redirect()->back()
                ->with('info', 'Vous avez déjà envoyé une demande récemment. Notre équipe vous recontactera sous peu.');
        }

        $validated = $request->validate([
            'service_types' => ['required', 'array', 'min:1'],
            'service_types.*' => ['string', 'max:50'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $serviceType = implode(', ', $validated['service_types']);

        // Stocke la demande détaillée pour le panel admin
        ContactMessage::create([
            'coach_id' => $coach->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $coach->phone ?? '',
            'service_type' => $serviceType,
            'message' => $validated['message'] ?? '',
            'is_read' => false,
        ]);

        // Marque le coach comme "contacté" pendant 7 jours
        $coach->custom_contact_locked_until = now()->addDays(7);
        $coach->save();

        return redirect()->back()
            ->with('success', 'Votre demande a été envoyée ! Nous vous recontacterons rapidement.');
    }
}
