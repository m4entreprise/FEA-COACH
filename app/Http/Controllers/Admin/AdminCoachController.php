<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AdminCoachController extends Controller
{
    /**
     * Display a listing of coaches.
     */
    public function index()
    {
        $coaches = Coach::with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($coach) {
                return [
                    'id' => $coach->id,
                    'name' => $coach->name,
                    'slug' => $coach->slug,
                    'subdomain' => $coach->subdomain,
                    'is_active' => $coach->is_active,
                    'user_email' => $coach->user?->email ?? 'N/A',
                    'user_name' => $coach->user?->name ?? 'N/A',
                    'is_fea_graduate' => $coach->user?->is_fea_graduate ?? false,
                    'subscription_status' => $coach->user?->subscription_status ?? null,
                    'trial_ends_at' => $coach->user?->trial_ends_at?->format('d/m/Y') ?? null,
                    'created_at' => $coach->created_at->format('d/m/Y'),
                ];
            });

        return Inertia::render('Admin/Coaches/Index', [
            'coaches' => $coaches,
        ]);
    }

    /**
     * Show the form for creating a new coach.
     */
    public function create()
    {
        return Inertia::render('Admin/Coaches/Create');
    }

    /**
     * Store a newly created coach in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'subdomain' => ['required', 'string', 'max:255', 'unique:coaches', 'regex:/^[a-z0-9\-]+$/'],
            'color_primary' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'color_secondary' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_active' => ['boolean'],
        ]);

        // Create user account for the coach
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'coach',
        ]);

        // Create coach profile
        $coach = Coach::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'subdomain' => $validated['subdomain'],
            'color_primary' => $validated['color_primary'],
            'color_secondary' => $validated['color_secondary'],
            'is_active' => $validated['is_active'] ?? true,
            'hero_title' => 'Transformez votre corps',
            'hero_subtitle' => 'Coaching sportif personnalisé',
            'about_text' => 'Présentation à compléter...',
            'method_text' => 'Ma méthode à compléter...',
            'cta_text' => 'Réservez votre séance découverte',
        ]);

        // Update user with coach_id
        $user->update(['coach_id' => $coach->id]);

        return redirect()
            ->route('admin.coaches.index')
            ->with('success', 'Coach créé avec succès.');
    }

    /**
     * Show the form for editing the specified coach.
     */
    public function edit(Coach $coach)
    {
        $coach->load('user');

        return Inertia::render('Admin/Coaches/Edit', [
            'coach' => [
                'id' => $coach->id,
                'name' => $coach->name,
                'slug' => $coach->slug,
                'subdomain' => $coach->subdomain,
                'color_primary' => $coach->color_primary,
                'color_secondary' => $coach->color_secondary,
                'is_active' => $coach->is_active,
                'user_id' => $coach->user_id,
                'user_email' => $coach->user->email,
                'user_name' => $coach->user->name,
            ],
        ]);
    }

    /**
     * Update the specified coach in storage.
     */
    public function update(Request $request, Coach $coach)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($coach->user_id)],
            'subdomain' => ['required', 'string', 'max:255', Rule::unique('coaches')->ignore($coach->id), 'regex:/^[a-z0-9\-]+$/'],
            'color_primary' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'color_secondary' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_active' => ['boolean'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        // Update user account
        $userUpdate = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $userUpdate['password'] = Hash::make($validated['password']);
        }

        $coach->user->update($userUpdate);

        // Update coach profile
        $coach->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'subdomain' => $validated['subdomain'],
            'color_primary' => $validated['color_primary'],
            'color_secondary' => $validated['color_secondary'],
            'is_active' => $validated['is_active'] ?? $coach->is_active,
        ]);

        return redirect()
            ->route('admin.coaches.index')
            ->with('success', 'Coach mis à jour avec succès.');
    }

    /**
     * Remove the specified coach from storage.
     */
    public function destroy(Coach $coach)
    {
        $user = $coach->user;
        
        // Delete coach (will cascade to related data)
        $coach->delete();
        
        // Delete user account
        $user->delete();

        return redirect()
            ->route('admin.coaches.index')
            ->with('success', 'Coach supprimé avec succès.');
    }
}
