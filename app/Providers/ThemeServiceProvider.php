<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class ThemeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadActiveTheme();
    }

    protected function loadActiveTheme(): void
    {
        try {
            $theme = option('active_theme', 'default');
            $themePath = base_path("themes/{$theme}");
            $viewsPath = $themePath . '/views';

            if (is_dir($viewsPath)) {
                $this->loadViewsFrom($viewsPath, 'theme');
                view()->prependNamespace('frontend', $viewsPath);
            }

            // Публикуем assets темы в public/themes/{theme}/
            $assetsSource = $themePath . '/assets';
            $assetsDest   = public_path("themes/{$theme}");

            if (is_dir($assetsSource) && !is_dir($assetsDest)) {
                File::copyDirectory($assetsSource, $assetsDest);
            }

        } catch (\Exception $e) {
            // БД недоступна при старте — молча используем дефолт
        }
    }
}