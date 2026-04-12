<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class WelcomeBanner extends Widget
{
    protected static string $view = 'filament.widgets.welcome-banner';
    protected static ?int $sort = -2;
    protected int|string|array $columnSpan = 'full';

    public bool $visible = true;
    public string $greeting = '';
    public string $timeOfDay = ''; // morning | afternoon | evening

    public function mount(): void
    {
        $this->visible = !auth()->user()->welcome_dismissed;

        $user = auth()->user();
        $tz = $user->timezone ?? 'UTC';

        $morningStart   = (int) ($user->morning_start   ?? 5);
        $afternoonStart = (int) ($user->afternoon_start ?? 12);
        $eveningStart   = (int) ($user->evening_start   ?? 18);
        $eveningStart   = (int) ($user->night_start   ?? 23);

        $hour = (int) now()->setTimezone($tz)->format('H');

        $this->timeOfDay = match(true) {
            $hour >= $morningStart   && $hour < $afternoonStart => 'morning',
            $hour >= $afternoonStart && $hour < $eveningStart   => 'afternoon',
            $hour >= $eveningStart   && $hour < 23              => 'evening',
            default                                              => 'night',
        };

        $this->greeting = match($this->timeOfDay) {
            'morning'   => 'Good morning',
            'afternoon' => 'Good afternoon',
            'evening'   => 'Good evening',
            default     => 'Good night',
        };
    }

    public function dismiss(): void
    {
        auth()->user()->update(['welcome_dismissed' => true]);
        $this->visible = false;
    }
}