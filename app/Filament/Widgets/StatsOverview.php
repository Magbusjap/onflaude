<?php

namespace App\Filament\Widgets;

use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = -1;

    protected function getStats(): array
    {
        return [
            Stat::make('Pages', Page::where('status', 'published')->count())
                ->description('Published pages')
                ->descriptionIcon('heroicon-m-document')
                ->color('primary'),

            Stat::make('Posts', Post::where('status', 'published')->count())
                ->description('Published posts')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success'),

            Stat::make('Users', User::count())
                ->description('Registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('warning'),
        ];
    }
}