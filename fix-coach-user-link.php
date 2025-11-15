<?php

/**
 * Script pour lier automatiquement les coachs à leurs utilisateurs
 * Exécutez : php fix-coach-user-link.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Coach;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== Correction des liens Coach <-> User ===\n\n";

// Récupérer tous les coachs sans user_id
$coachesWithoutUser = Coach::whereNull('user_id')->get();

echo "Nombre de coachs à corriger : " . $coachesWithoutUser->count() . "\n\n";

if ($coachesWithoutUser->isEmpty()) {
    echo "✅ Tous les coachs sont déjà liés à un utilisateur !\n";
    exit(0);
}

foreach ($coachesWithoutUser as $coach) {
    echo "─────────────────────────────────────\n";
    echo "Coach ID: {$coach->id}\n";
    echo "Coach Name: {$coach->name}\n";
    echo "Coach Subdomain: {$coach->subdomain}\n";
    
    // Chercher un user qui a ce coach_id dans sa table
    $user = User::where('coach_id', $coach->id)->first();
    
    if ($user) {
        echo "✅ User trouvé via coach_id: {$user->email} (ID: {$user->id})\n";
        
        // Mettre à jour le coach avec le user_id
        $coach->user_id = $user->id;
        $coach->save();
        
        echo "✅ Lien créé : Coach #{$coach->id} -> User #{$user->id}\n";
        
        if ($user->vat_number) {
            echo "✅ N° TVA disponible : {$user->vat_number}\n";
        } else {
            echo "⚠️  N° TVA non renseigné pour cet utilisateur\n";
        }
    } else {
        echo "❌ Aucun utilisateur trouvé avec coach_id={$coach->id}\n";
        echo "   Tentative de recherche par email/nom...\n";
        
        // Essayer de trouver par nom
        $possibleUser = User::where('email', 'LIKE', '%' . strtolower($coach->name) . '%')
                            ->orWhere('name', 'LIKE', '%' . $coach->name . '%')
                            ->first();
        
        if ($possibleUser) {
            echo "   User possible trouvé : {$possibleUser->email} (ID: {$possibleUser->id})\n";
            echo "   Voulez-vous lier ce coach à cet utilisateur ? (y/N) : ";
            
            $handle = fopen("php://stdin", "r");
            $line = fgets($handle);
            
            if (trim(strtolower($line)) === 'y') {
                $coach->user_id = $possibleUser->id;
                $coach->save();
                echo "   ✅ Lien créé manuellement !\n";
            } else {
                echo "   ⏭️  Ignoré\n";
            }
            
            fclose($handle);
        } else {
            echo "   ❌ Aucun utilisateur correspondant trouvé\n";
        }
    }
    
    echo "\n";
}

echo "\n=== Récapitulatif ===\n\n";

$fixed = Coach::whereNotNull('user_id')->count();
$total = Coach::count();

echo "Coachs liés : {$fixed} / {$total}\n";

if ($fixed < $total) {
    echo "\n⚠️  Il reste des coachs non liés. Vous devrez les lier manuellement.\n";
} else {
    echo "\n✅ Tous les coachs sont maintenant liés !\n";
}

echo "\n=== FIN ===\n";
