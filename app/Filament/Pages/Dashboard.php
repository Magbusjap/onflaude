<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\WelcomeBanner;
use Filament\Pages\Dashboard as BaseDashboard;
use Livewire\Attributes\Renderless;

class Dashboard extends BaseDashboard
{
    protected static string $view = 'admin::pages.dashboard';

    public function getWidgets(): array
    {
        return [
            WelcomeBanner::class,
            StatsOverview::class,
        ];
    }

    public function getColumns(): int|array
    {
        return 1;
    }

    public static function getAvailableWidgets(): array
    {
        return [
            'welcome_banner' => [
                'label' => 'Welcome Banner',
                'class' => WelcomeBanner::class,
            ],
            'stats_overview' => [
                'label' => 'Stats Overview',
                'class' => StatsOverview::class,
            ],
        ];
    }

    public function getActiveWidgetKeys(): array
    {
        $saved = auth()->user()->dashboard_widgets;
        if ($saved) {
            return is_array($saved) ? $saved : json_decode($saved, true);
        }
        return array_keys(static::getAvailableWidgets());
    }

    #[Renderless]
    public function saveWidgetPreferences(array $activeKeys): void
    {
        auth()->user()->update(['dashboard_widgets' => $activeKeys]);
    }

    public function getVisibleWidgets(): array
    {
        $activeKeys = $this->getActiveWidgetKeys();
        $available = static::getAvailableWidgets();

        return collect($activeKeys)
            ->filter(fn($key) => isset($available[$key]))
            ->map(fn($key) => $available[$key]['class'])
            ->values()
            ->toArray();
    }
}