<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupportTicketController extends Controller
{
    /**
     * Display all support tickets for admins.
     */
    public function index(Request $request)
    {
        $statusFilter = $request->input('status');

        $ticketsQuery = SupportTicket::query()
            ->with(['user', 'messages.sender'])
            ->orderByRaw("CASE WHEN status = 'open' THEN 0 ELSE 1 END")
            ->orderByDesc('last_message_at')
            ->orderByDesc('created_at');

        if ($statusFilter) {
            $ticketsQuery->where('status', $statusFilter);
        }

        $tickets = $ticketsQuery->get()->map(function (SupportTicket $ticket) {
            return [
                'id' => $ticket->id,
                'subject' => $ticket->subject,
                'category' => $ticket->category,
                'status' => $ticket->status,
                'created_at' => $ticket->created_at->toDateTimeString(),
                'last_message_at' => optional($ticket->last_message_at)->toDateTimeString(),
                'closed_at' => optional($ticket->closed_at)->toDateTimeString(),
                'user' => [
                    'id' => $ticket->user->id,
                    'name' => $ticket->user->name,
                    'email' => $ticket->user->email,
                ],
                'messages' => $ticket->messages->map(function (SupportMessage $message) {
                    return [
                        'id' => $message->id,
                        'message' => $message->message,
                        'is_from_admin' => $message->is_from_admin,
                        'created_at' => $message->created_at->toDateTimeString(),
                        'sender_name' => $message->sender?->name ?? ($message->is_from_admin ? 'Support FEA' : 'Coach'),
                    ];
                }),
            ];
        });

        $stats = [
            'total' => SupportTicket::count(),
            'open' => SupportTicket::where('status', 'open')->count(),
            'closed' => SupportTicket::where('status', 'closed')->count(),
        ];

        return Inertia::render('Admin/SupportTickets/Index', [
            'tickets' => $tickets,
            'stats' => $stats,
            'filters' => [
                'status' => $statusFilter,
            ],
        ]);
    }

    /**
     * Add a reply from an admin to the given ticket.
     */
    public function reply(Request $request, SupportTicket $supportTicket)
    {
        $validated = $request->validate([
            'message' => ['required', 'string'],
        ]);

        $admin = $request->user();

        SupportMessage::create([
            'support_ticket_id' => $supportTicket->id,
            'sender_id' => $admin->id,
            'is_from_admin' => true,
            'message' => $validated['message'],
        ]);

        $supportTicket->update([
            'status' => 'open',
            'last_message_at' => now(),
        ]);

        return redirect()
            ->route('admin.support-tickets.index', ['status' => $request->input('status')])
            ->with('success', 'Réponse envoyée au coach.');
    }

    /**
     * Update the status of a ticket (open/closed).
     */
    public function updateStatus(Request $request, SupportTicket $supportTicket)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:open,closed'],
        ]);

        $supportTicket->update([
            'status' => $validated['status'],
            'closed_at' => $validated['status'] === 'closed' ? now() : null,
        ]);

        return redirect()
            ->route('admin.support-tickets.index', ['status' => $request->input('status')])
            ->with('success', 'Statut du ticket mis à jour.');
    }
}
