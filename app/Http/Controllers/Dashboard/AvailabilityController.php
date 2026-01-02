<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AvailabilitySlot;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AvailabilityController extends Controller
{
    public function index(Request $request)
    {
        $coach = $request->user()->coach;

        if (!$coach) {
            return redirect()->route('dashboard');
        }

        $slots = $coach->availabilitySlots()
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        $groupedSlots = [];
        for ($day = 1; $day <= 7; $day++) {
            $daySlots = $slots->get($day === 7 ? 0 : $day, collect());
            $groupedSlots[] = [
                'day' => $day === 7 ? 0 : $day,
                'slots' => $daySlots,
            ];
        }

        return Inertia::render('Dashboard/Availability', [
            'availability' => $groupedSlots,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'day_of_week' => 'required|integer|min:0|max:6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_active' => 'boolean',
        ]);

        $coach = $request->user()->coach;

        $existing = $coach->availabilitySlots()
            ->where('day_of_week', $validated['day_of_week'])
            ->where('start_time', $validated['start_time'])
            ->exists();

        if ($existing) {
            return back()->with('error', 'Ce créneau existe déjà');
        }

        $coach->availabilitySlots()->create($validated);

        return back()->with('success', 'Créneau ajouté avec succès');
    }

    public function update(Request $request, AvailabilitySlot $slot)
    {
        $this->authorize('update', $slot);

        $validated = $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_active' => 'boolean',
        ]);

        $slot->update($validated);

        return back()->with('success', 'Créneau mis à jour avec succès');
    }

    public function destroy(AvailabilitySlot $slot)
    {
        $this->authorize('delete', $slot);

        $slot->delete();

        return back()->with('success', 'Créneau supprimé avec succès');
    }
}
