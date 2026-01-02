<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactRequestController extends Controller
{
    /**
     * Display a listing of contact requests.
     */
    public function index()
    {
        $requests = ContactMessage::with('coach.user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($request) => [
                'id' => $request->id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'message' => $request->message,
                'is_read' => $request->is_read,
                'coach_id' => $request->coach_id,
                'coach_name' => $request->coach?->name ?? 'Coach supprimé',
                'coach_email' => $request->coach?->user?->email ?? 'N/A',
                'created_at' => $request->created_at->format('d/m/Y H:i'),
            ]);

        return Inertia::render('Admin/ContactRequests', [
            'requests' => $requests,
        ]);
    }

    /**
     * Mark a request as read.
     */
    public function markAsRead(ContactMessage $contactMessage)
    {
        $contactMessage->update(['is_read' => true]);

        return redirect()
            ->route('admin.contact-requests.index')
            ->with('success', 'Demande marquée comme lue.');
    }

    /**
     * Delete a contact request.
     */
    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();

        return redirect()
            ->route('admin.contact-requests.index')
            ->with('success', 'Demande supprimée avec succès.');
    }
}
