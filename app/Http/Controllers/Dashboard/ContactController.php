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
}
