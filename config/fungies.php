<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Fungies.io API Keys
    |--------------------------------------------------------------------------
    |
    | Your Fungies.io API keys. Get them from:
    | https://fungies.io/dashboard/developers
    |
    | - API Key (public): For read operations (GET requests)
    | - Write API Key (secret): For write operations (POST, PATCH, DELETE)
    |
    */

    'api_key' => env('FUNGIES_API_KEY'),

    'write_api_key' => env('FUNGIES_WRITE_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Fungies.io Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for Fungies.io API.
    | All API requests are made to this endpoint.
    |
    */

    'base_url' => env('FUNGIES_BASE_URL', 'https://api.fungies.io'),

    /*
    |--------------------------------------------------------------------------
    | Fungies.io Plan ID
    |--------------------------------------------------------------------------
    |
    | The ID of your subscription plan on Fungies.io.
    | This is the plan that coaches will subscribe to (20€ HTVA/mois).
    |
    */

    'plan_id' => env('FUNGIES_PLAN_ID'),

    /*
    |--------------------------------------------------------------------------
    | Fungies.io SKUs
    |--------------------------------------------------------------------------
    |
    | SKUs for different user types. The SKU determines the pricing and
    | trial period in Fungies.io:
    |
    | - fea-coach-pro-graduate: For FEA graduates (with trial period)
    | - fea-coach-pro-standard: For non-FEA users
    |
    */

    'sku_fea' => env('FUNGIES_SKU_FEA', 'fea-coach-pro-graduate'),

    'sku_standard' => env('FUNGIES_SKU_STANDARD', 'fea-coach-pro-standard'),

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Webhook secret used to verify incoming webhook requests from Fungies.io.
    | This ensures that webhook requests are legitimate.
    |
    */

    'webhook_secret' => env('FUNGIES_WEBHOOK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Redirect URLs
    |--------------------------------------------------------------------------
    |
    | URLs to redirect users after successful or cancelled checkout.
    |
    */

    'checkout_success_url' => env('APP_URL') . '/dashboard?subscription=success',

    'checkout_cancel_url' => env('APP_URL') . '/dashboard/subscription?cancelled=true',

];
