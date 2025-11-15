<?php

/**
 * Script de vérification du N° TVA
 * Exécutez : php check-vat.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Coach;
use App\Models\User;

echo "=== Vérification de la structure N° TVA ===\n\n";

// Récupérer tous les coachs
$coaches = Coach::all();

echo "Nombre de coachs : " . $coaches->count() . "\n\n";

foreach ($coaches as $coach) {
    echo "─────────────────────────────────────\n";
    echo "Coach ID: {$coach->id}\n";
    echo "Coach Name: {$coach->name}\n";
    echo "Coach Subdomain: {$coach->subdomain}\n";
    echo "Coach user_id: " . ($coach->user_id ?? 'NULL') . "\n";
    
    // Vérifier si le user existe
    if ($coach->user_id) {
        $user = User::find($coach->user_id);
        if ($user) {
            echo "✅ User trouvé: {$user->email}\n";
            echo "   N° TVA: " . ($user->vat_number ?? 'PAS DE TVA') . "\n";
        } else {
            echo "❌ User introuvable avec ID {$coach->user_id}\n";
        }
    } else {
        echo "❌ Aucun user_id défini pour ce coach\n";
    }
    
    echo "\n";
}

// Récupérer tous les users qui ont un N° TVA
echo "\n=== Users avec N° TVA ===\n\n";
$usersWithVat = User::whereNotNull('vat_number')->get();

foreach ($usersWithVat as $user) {
    echo "User ID: {$user->id} | Email: {$user->email} | TVA: {$user->vat_number}\n";
    
    // Vérifier s'il a un coach lié
    $linkedCoach = Coach::where('user_id', $user->id)->first();
    if ($linkedCoach) {
        echo "  ✅ Lié au coach: {$linkedCoach->name} (ID: {$linkedCoach->id})\n";
    } else {
        echo "  ❌ Aucun coach lié à cet utilisateur\n";
    }
}

echo "\n=== FIN ===\n";
