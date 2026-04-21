<?php

/*
|--------------------------------------------------------------------------
| OnFlaude CMS — Platform configuration
|--------------------------------------------------------------------------
|
| Paths, active theme and locale. Consumed by the core and by extensions
| (themes, plugins, Python services).
|
*/

return [

    'version' => '0.1.0-dev',

    'paths' => [
        'themes'   => base_path('themes'),
        'plugins'  => base_path('plugins'),
        'services' => base_path('services'),
        'media'    => storage_path('app/media'),
    ],

    'theme' => [
        'active'   => env('ONFLAUDE_THEME', 'default'),
        'fallback' => 'default',
    ],

    'locale' => [
        'default'  => 'en',
        'fallback' => 'en',
    ],

];
