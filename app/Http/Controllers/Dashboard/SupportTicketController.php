<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupportTicketController extends Controller
{
    /**
     * Display the list of support tickets for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $tickets = SupportTicket::where('user_id', $user->id)
            ->with(['messages.sender'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(function (SupportTicket $ticket) {
                return [
                    'id' => $ticket->id,
                    'subject' => $ticket->subject,
                    'category' => $ticket->category,
                    'status' => $ticket->status,
                    'created_at' => $ticket->created_at->toDateTimeString(),
                    'last_message_at' => optional($ticket->last_message_at)->toDateTimeString(),
                    'closed_at' => optional($ticket->closed_at)->toDateTimeString(),
                    'messages' => $ticket->messages->map(function (SupportMessage $message) {
                        return [
                            'id' => $message->id,
                            'message' => $message->message,
                            'is_from_admin' => $message->is_from_admin,
                            'created_at' => $message->created_at->toDateTimeString(),
                            'sender_name' => $message->sender?->name ?? ($message->is_from_admin ? 'Support FEA' : 'Vous'),
                        ];
                    }),
                ];
            });

        return Inertia::render('Dashboard/Support', [
            'tickets' => $tickets,
        ]);
    }

    /**
     * Store a newly created support ticket and its first message.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'subject' => $validated['subject'],
            'category' => $validated['category'] ?? null,
            'status' => 'open',
            'last_message_at' => now(),
        ]);

        SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'sender_id' => $user->id,
            'is_from_admin' => false,
            'message' => $validated['message'],
        ]);

        return redirect()
            ->route('dashboard.support')
            ->with('success', 'Votre demande de support a été envoyée. Nous reviendrons vers vous rapidement.');
    }

    /**
     * Add a reply from the authenticated user to an existing ticket.
     */
    public function reply(Request $request, SupportTicket $supportTicket)
    {
        $user = $request->user();

        if ($supportTicket->user_id !== $user->id) {
            abort(403, 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'message' => ['required', 'string'],
        ]);

        SupportMessage::create([
            'support_ticket_id' => $supportTicket->id,
            'sender_id' => $user->id,
            'is_from_admin' => false,
            'message' => $validated['message'],
        ]);

        $supportTicket->update([
            'status' => $supportTicket->status === 'closed' ? 'open' : $supportTicket->status,
            'last_message_at' => now(),
        ]);

        return redirect()
            ->route('dashboard.support')
            ->with('success', 'Votre message a été envoyé.');
    }

    /**
     * Allow the user to close a ticket.
     */
    public function close(Request $request, SupportTicket $supportTicket)
    {
        $user = $request->user();

        if ($supportTicket->user_id !== $user->id) {
            abort(403, 'Accès non autorisé.');
        }

        $supportTicket->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return redirect()
            ->route('dashboard.support')
            ->with('success', 'La conversation de support a été clôturée.');
    }
}
