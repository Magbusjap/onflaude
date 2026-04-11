<?php

use App\Models\User;

test('administrator can access filament panel', function () {
    $user = User::factory()->make(['role' => 'administrator']);
    expect($user->canAccessPanel(app(\Filament\Panel::class)))->toBeTrue();
});

test('editor cannot access filament panel', function () {
    $user = User::factory()->make(['role' => 'editor']);
    expect($user->canAccessPanel(app(\Filament\Panel::class)))->toBeFalse();
});

test('author cannot access filament panel', function () {
    $user = User::factory()->make(['role' => 'author']);
    expect($user->canAccessPanel(app(\Filament\Panel::class)))->toBeFalse();
});

test('isAdmin() returns true only for administrator', function () {
    expect(User::factory()->make(['role' => 'administrator'])->isAdmin())->toBeTrue();
    expect(User::factory()->make(['role' => 'editor'])->isAdmin())->toBeFalse();
    expect(User::factory()->make(['role' => 'author'])->isAdmin())->toBeFalse();
});

test('hasRole() matches exact role', function () {
    $user = User::factory()->make(['role' => 'editor']);
    expect($user->hasRole('editor'))->toBeTrue();
    expect($user->hasRole('administrator'))->toBeFalse();
});
