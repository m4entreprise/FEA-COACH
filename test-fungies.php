<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;

echo "🧪 Test de connexion Fungies.io\n\n";

$apiKey = config('fungies.api_key');
$writeApiKey = config('fungies.write_api_key');
$planId = config('fungies.plan_id');
$baseUrl = config('fungies.base_url');

echo "Configuration:\n";
echo "- Base URL: $baseUrl\n";
echo "- API Key: " . substr($apiKey, 0, 20) . "...\n";
echo "- Plan ID: $planId\n\n";

echo "Test 1: Liste des abonnements (GET)...\n";
try {
    $response = Http::withHeaders([
        'x-api-key' => $apiKey,
        'Content-Type' => 'application/json',
    ])->get("$baseUrl/subscriptions/list", [
        'status' => 'all',
        'take' => 1,
    ]);

    echo "Status: " . $response->status() . "\n";
    echo "Response: " . $response->body() . "\n\n";

    if ($response->successful()) {
        echo "✅ API Read fonctionne!\n\n";
    } else {
        echo "❌ Erreur API Read\n\n";
    }
} catch (\Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n\n";
}

echo "Test 2: Création d'un ordre (POST)...\n";
try {
    $response = Http::withHeaders([
        'x-write-api-key' => $writeApiKey,
        'Content-Type' => 'application/json',
    ])->post("$baseUrl/orders", [
        'planId' => $planId,
        'email' => 'test@example.com',
        'name' => 'Test User',
        'sku' => config('fungies.sku_standard'),
        'metadata' => [
            'test' => true,
        ],
    ]);

    echo "Status: " . $response->status() . "\n";
    echo "Response: " . $response->body() . "\n\n";

    if ($response->successful()) {
        echo "✅ API Write fonctionne!\n";
    } else {
        echo "❌ Erreur API Write\n";
        echo "Raison possible: Store inactif, Plan inactif, ou SKU invalide\n";
    }
} catch (\Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}
