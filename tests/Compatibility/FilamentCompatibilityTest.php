<?php

use Filament\Panel;
use Filament\Models\Contracts\FilamentUser;

/**
 * This test suite checks that the public API of Filament 
 * that we use in OnFlaude has not changed.
 * Run after every: composer update filament/filament
 */

test('[COMPAT] Panel::brandLogo() method exists', function () {
    expect(method_exists(Panel::class, 'brandLogo'))->toBeTrue();
})->group('compatibility');

test('[COMPAT] Panel::favicon() method exists', function () {
    expect(method_exists(Panel::class, 'favicon'))->toBeTrue();
})->group('compatibility');

test('[COMPAT] Panel::colors() method exists', function () {
    expect(method_exists(Panel::class, 'colors'))->toBeTrue();
})->group('compatibility');

test('[COMPAT] Panel::path() method exists', function () {
    expect(method_exists(Panel::class, 'path'))->toBeTrue();
})->group('compatibility');

test('[COMPAT] FilamentUser interface has canAccessPanel()', function () {
    expect(method_exists(FilamentUser::class, 'canAccessPanel'))->toBeTrue();
})->group('compatibility');

test('[COMPAT] renderHook panels::head.end is valid hook name', function () {
    //Check that Filament recognizes this hook
    expect(class_exists(\Filament\View\PanelsRenderHook::class))->toBeTrue();
    
    $reflection = new ReflectionClass(\Filament\View\PanelsRenderHook::class);
    $constants = $reflection->getConstants();
    
    expect($constants)->toHaveKey('HEAD_END');
})->group('compatibility');
