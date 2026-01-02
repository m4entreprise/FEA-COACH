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
     * Display a listing of all users (coaches and pending)
     */
    public function index()
    {
        // Liste TOUS les utilisateurs non-admin, avec leur profil coach s'il existe
        $users = User::with('coach')
            ->where('role', '!=', 'admin')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($user) {
                $coach = $user->coach;
                
                return [
                    'id' => $user->id,
                    'user_id' => $user->id,
                    'coach_id' => $coach?->id,
                    
                    // Infos utilisateur
                    'email' => $user->email,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'full_name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->name,
                    
                    // Statuts
                    'is_fea_graduate' => $user->is_fea_graduate ?? false,
                    'onboarding_completed' => $user->onboarding_completed ?? false,
                    'setup_completed' => $user->setup_completed ?? false,
                    'subscription_status' => $user->subscription_status,
                    'trial_ends_at' => $user->trial_ends_at?->format('d/m/Y'),
                    'trial_expired' => $user->trial_ends_at ? $user->trial_ends_at->isPast() : false,
                    'trial_days_left' => $user->trial_ends_at && !$user->trial_ends_at->isPast() 
                        ? $user->trial_ends_at->diffInDays(now()) 
                        : null,
                    
                    // Infos coach si existe
                    'has_coach_profile' => $coach !== null,
                    'coach_name' => $coach?->name,
                    'slug' => $coach?->slug,
                    'subdomain' => $coach?->subdomain ?? $coach?->slug,
                    'is_active' => $coach?->is_active ?? false,
                    
                    // Dates
                    'created_at' => $user->created_at->format('d/m/Y H:i'),
                ];
            });

        return Inertia::render('Admin/Coaches/Index', [
            'users' => $users,
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
            'slug' => $validated['subdomain'], // Use subdomain as slug for consistency
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

        // Si le coach n'a pas de user, on redirige avec une erreur
        if (!$coach->user) {
            return redirect()
                ->route('admin.coaches.index')
                ->with('error', 'Ce coach n\'a pas de compte utilisateur associé. Veuillez le supprimer ou le recréer.');
        }

        $trialEndsAt = $coach->user->trial_ends_at;

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
                'is_fea_graduate' => $coach->user->is_fea_graduate ?? false,
                'trial_ends_at' => $trialEndsAt?->format('Y-m-d') ?? null,
                'trial_display' => $trialEndsAt?->format('d/m/Y') ?? null,
                'trial_expired' => $trialEndsAt?->isPast() ?? null,
                'trial_days_left' => $trialEndsAt && !$trialEndsAt->isPast()
                    ? $trialEndsAt->diffInDays(now())
                    : null,
            ],
        ]);
    }

    /**
     * Update the specified coach in storage.
     */
    public function update(Request $request, Coach $coach)
    {
        // Vérifier que le coach a un user
        if (!$coach->user) {
            return redirect()
                ->route('admin.coaches.index')
                ->with('error', 'Ce coach n\'a pas de compte utilisateur associé.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($coach->user_id)],
            'subdomain' => ['required', 'string', 'max:255', Rule::unique('coaches')->ignore($coach->id), 'regex:/^[a-z0-9\-]+$/'],
            'color_primary' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'color_secondary' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_active' => ['boolean'],
            'password' => ['nullable', 'string', 'min:8'],
            'is_fea_graduate' => ['boolean'],
        ]);

        // Update user account
        $userUpdate = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_fea_graduate' => $validated['is_fea_graduate'] ?? false,
        ];

        if (!empty($validated['password'])) {
            $userUpdate['password'] = Hash::make($validated['password']);
        }

        $coach->user->update($userUpdate);

        // Update coach profile
        $coach->update([
            'name' => $validated['name'],
            'slug' => $validated['subdomain'], // Use subdomain as slug for consistency
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
        
        // Delete user account if exists
        if ($user) {
            $user->delete();
        }

        return redirect()
            ->route('admin.coaches.index')
            ->with('success', 'Coach supprimé avec succès.');
    }
}
