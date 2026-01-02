<?php

return [
    'api_key' => env('LEMON_SQUEEZY_API_KEY'),
    'store_id' => env('LEMON_SQUEEZY_STORE_ID'),
    'store_domain' => env('LEMON_SQUEEZY_STORE_DOMAIN', 'unicoach.lemonsqueezy.com'),
    'variant_non_fea' => env('LEMON_SQUEEZY_VARIANT_NON_FEA'),
    'variant_fea' => env('LEMON_SQUEEZY_VARIANT_FEA'),
    'variant_custom_domain' => env('LEMON_SQUEEZY_NOM_DOMAINE'),
    'webhook_secret' => env('LEMON_SQUEEZY_WEBHOOK_SECRET'),
    'base_url' => env('LEMON_SQUEEZY_BASE_URL', 'https://api.lemonsqueezy.com/v1'),
];
