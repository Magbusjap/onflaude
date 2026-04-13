<?php

namespace App\Filament\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasGlobalSearch
{
    protected static bool $globallySearchable = true;

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->title ?? $record->name ?? '';
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [];
    }
}