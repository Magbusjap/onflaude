<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

test('option() returns default when key does not exist', function () {
    expect(option('nonexistent_key', 'default_value'))->toBe('default_value');
});

test('option() returns value from database', function () {
    DB::table('options')->insert([
        'key'   => 'test_key',
        'value' => 'test_value',
        'group' => 'general',
    ]);

    Cache::forget('option_test_key');

    expect(option('test_key'))->toBe('test_value');
});

test('option() caches value after first read', function () {
    DB::table('options')->insert([
        'key'   => 'cached_key',
        'value' => 'cached_value',
        'group' => 'general',
    ]);

    Cache::forget('option_cached_key');
    option('cached_key'); //first call - writes to cache

    DB::table('options')->where('key', 'cached_key')->update(['value' => 'new_value']);

    // cache should return old value
    expect(option('cached_key'))->toBe('cached_value');
});

test('set_option() writes value and clears cache', function () {
    set_option('write_key', 'initial', 'general');
    expect(option('write_key'))->toBe('initial');

    set_option('write_key', 'updated', 'general');
    expect(option('write_key'))->toBe('updated');
});
