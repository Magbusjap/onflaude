<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

if (!function_exists('option')) {
    function option(string $key, mixed $default = null): mixed
    {
        try {
            return Cache::rememberForever("option_{$key}", function () use ($key, $default) {
                $option = DB::table('options')->where('key', $key)->first();
                return $option ? $option->value : $default;
            });
        } catch (\Exception $e) {
            return $default;
        }
    }
}

if (!function_exists('set_option')) {
    function set_option(string $key, mixed $value, string $group = 'general'): void
    {
        DB::table('options')->updateOrInsert(
            ['key' => $key],
            ['value' => $value, 'group' => $group, 'updated_at' => now()]
        );
        Cache::forget("option_{$key}");
    }
}
