<?php

return [
    'public_key' => env('STRIPE_PUBLIC_KEY'),
    'secret_key' => env('STRIPE_SECRET_KEY'),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    
    'platform_commission_rate' => env('STRIPE_PLATFORM_COMMISSION_RATE', 0.0),
    
    'payments_module_price' => 5.00,
    
    'currency' => env('STRIPE_CURRENCY', 'EUR'),
];
