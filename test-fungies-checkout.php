<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;

echo "🧪 Test création Checkout Fungies.io\n\n";

$writeApiKey = config('fungies.write_api_key');
$planId = config('fungies.plan_id');
$baseUrl = config('fungies.base_url');

// Test 1: Format basique
echo "Test 1: Format basique...\n";
$response1 = Http::withHeaders([
    'x-write-api-key' => $writeApiKey,
    'Content-Type' => 'application/json',
])->post("$baseUrl/v0/elements/checkout/create", [
    'planId' => $planId,
    'email' => 'test@example.com',
]);

echo "Status: " . $response1->status() . "\n";
echo "Response: " . $response1->body() . "\n\n";

// Test 2: Avec offers array
echo "Test 2: Avec offers array...\n";
$response2 = Http::withHeaders([
    'x-write-api-key' => $writeApiKey,
    'Content-Type' => 'application/json',
])->post("$baseUrl/v0/elements/checkout/create", [
    'offers' => [
        [
            'planId' => $planId,
            'quantity' => 1,
        ]
    ],
    'customer' => [
        'email' => 'test@example.com',
        'name' => 'Test User',
    ],
]);

echo "Status: " . $response2->status() . "\n";
echo "Response: " . $response2->body() . "\n\n";

// Test 3: Format simple avec metadata
echo "Test 3: Avec metadata...\n";
$response3 = Http::withHeaders([
    'x-write-api-key' => $writeApiKey,
    'Content-Type' => 'application/json',
])->post("$baseUrl/v0/elements/checkout/create", [
    'planId' => $planId,
    'customerEmail' => 'test@example.com',
    'metadata' => [
        'user_id' => '123',
        'source' => 'test',
    ],
]);

echo "Status: " . $response3->status() . "\n";
echo "Response: " . $response3->body() . "\n\n";

// Test 4: Vérifier l'erreur pour comprendre le format
echo "Test 4: Minimal pour voir l'erreur...\n";
$response4 = Http::withHeaders([
    'x-write-api-key' => $writeApiKey,
    'Content-Type' => 'application/json',
])->post("$baseUrl/v0/elements/checkout/create", []);

echo "Status: " . $response4->status() . "\n";
echo "Response: " . $response4->body() . "\n";
