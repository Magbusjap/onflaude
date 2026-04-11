<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class WelcomeBanner extends Widget
{
    protected static string $view = 'filament.widgets.welcome-banner';
    protected static ?int $sort = -2;
    protected int | string | array $columnSpan = 'full';

    public bool $visible = true;

    public function mount(): void
    {
        $this->visible = !auth()->user()->welcome_dismissed;
    }

    public function dismiss(): void
    {
        auth()->user()->update(['welcome_dismissed' => true]);
        $this->visible = false;
    }
}