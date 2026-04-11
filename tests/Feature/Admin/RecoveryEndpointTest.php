<?php

use Illuminate\Support\Facades\DB;

test('recovery page is accessible', function () {
    $this->get('/onflaude-recovery')->assertOk();
});

test('recovery rejects wrong secret key', function () {
    $this->post('/onflaude-recovery', [
        'secret'   => 'wrongkey12345678',
        'new_path' => 'new-admin',
    ])->assertSessionHasErrors('secret');
});

test('recovery accepts correct secret and updates admin_path', function () {
    // Get the correct key the same way as in the controller
    $appKey = substr(config('app.key'), 7, 16);

    set_option('admin_path', 'old-path', 'system');

    $this->post('/onflaude-recovery', [
        'secret'   => $appKey,
        'new_path' => 'new-path',
    ])->assertRedirect('/new-path');

    expect(option('admin_path'))->toBe('new-path');
});

test('recovery rejects invalid path format', function () {
    $appKey = substr(config('app.key'), 7, 16);

    $this->post('/onflaude-recovery', [
        'secret'   => $appKey,
        'new_path' => 'invalid path!',
    ])->assertSessionHasErrors('new_path');
});
