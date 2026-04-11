<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
            $viewsPath = base_path("themes/{$theme}/views");

            if (is_dir($viewsPath)) {
                //Theme views will be loaded with the 'theme::'
                //  namespace, e.g. view('theme::index')
                $this->loadViewsFrom($viewsPath, 'theme');

                // standard views will be loaded without namespace, e.g. view('index')
                view()->prependNamespace('frontend', $viewsPath);
            }
        } catch (\Exception $e) {
            // Default to 'default' theme if there's an error loading the active theme
        }
    }
}