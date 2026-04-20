<?php

/*
|--------------------------------------------------------------------------
| OnFlaude CMS — настройки платформы
|--------------------------------------------------------------------------
|
| Конфигурация путей, активной темы и локали. Используется ядром и
| расширениями (темами, плагинами, Python-сервисами).
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
        'default'  => 'ru',
        'fallback' => 'en',
    ],

];
