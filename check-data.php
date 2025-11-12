<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Coach;

echo "\n=== VÉRIFICATION DES DONNÉES ===\n\n";

$coaches = Coach::all();

foreach ($coaches as $coach) {
    echo "Coach: {$coach->name} (ID: {$coach->id})\n";
    echo "  Slug: {$coach->slug}\n";
    echo "  Subdomain: {$coach->subdomain}\n";
    
    $plansCount = $coach->plans()->count();
    $activePlansCount = $coach->plans()->where('is_active', true)->count();
    $transformationsCount = $coach->transformations()->count();
    
    echo "  Plans (total): {$plansCount}\n";
    echo "  Plans (actifs): {$activePlansCount}\n";
    echo "  Transformations: {$transformationsCount}\n";
    
    if ($plansCount > 0) {
        echo "  Liste des plans:\n";
        foreach ($coach->plans as $plan) {
            $status = $plan->is_active ? 'ACTIF' : 'INACTIF';
            echo "    - {$plan->name} ({$plan->price}€) [{$status}]\n";
        }
    }
    
    echo "\n";
}

echo "=== FIN ===\n";
