<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->registerThemeViewNamespace();
        $this->registerAdminViewNamespace();
    }

    /**
     * Регистрирует namespace theme:: для Blade активной темы.
     * Приоритет: themes/{active}/views -> themes/{fallback}/views
     */
    protected function registerThemeViewNamespace(): void
    {
        $themesPath = config('onflaude.paths.themes', base_path('themes'));
        $active     = config('onflaude.theme.active', 'default');
        $fallback   = config('onflaude.theme.fallback', 'default');

        $paths = [];

        if ($active && is_dir("{$themesPath}/{$active}/views")) {
            $paths[] = "{$themesPath}/{$active}/views";
        }

        if ($fallback && $fallback !== $active && is_dir("{$themesPath}/{$fallback}/views")) {
            $paths[] = "{$themesPath}/{$fallback}/views";
        }

        if (!empty($paths)) {
            View::addNamespace('theme', $paths);
        }
    }

    /**
     * Регистрирует namespace admin:: для Blade админки Filament.
     * Путь: resources/admin/views/
     */
    protected function registerAdminViewNamespace(): void
    {
        $adminViews = base_path('resources/admin/views');

        if (is_dir($adminViews)) {
            View::addNamespace('admin', $adminViews);
        }
    }
}
