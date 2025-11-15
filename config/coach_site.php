<?php

return [
    'default_layout' => 'classic',

    'layouts' => [
        'classic' => [
            'label' => 'Classique',
            'description' => 'Layout actuel, équilibré et polyvalent.',
            'view' => 'coach-site.layouts.classic',
            'preview_image' => '/images/layouts/classic.png',
        ],
        'minimal' => [
            'label' => 'Minimal',
            'description' => 'Layout épuré, très focalisé sur le texte et les CTA.',
            'view' => 'coach-site.layouts.minimal',
            'preview_image' => '/images/layouts/minimal.png',
        ],
        'bold' => [
            'label' => 'Impact',
            'description' => 'Layout très visuel avec de grosses sections hero.',
            'view' => 'coach-site.layouts.bold',
            'preview_image' => '/images/layouts/bold.png',
        ],
    ],
];
