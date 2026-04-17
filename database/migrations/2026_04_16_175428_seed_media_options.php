<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            ['key' => 'media_max_upload_size',    'value' => '10',     'group' => 'media'],
            ['key' => 'media_allowed_types',      'value' => 'images', 'group' => 'media'],
            ['key' => 'media_convert_to_webp',    'value' => '0',      'group' => 'media'],
            ['key' => 'media_jpeg_quality',       'value' => '85',     'group' => 'media'],
            ['key' => 'media_strip_exif',         'value' => '1',      'group' => 'media'],
            ['key' => 'media_sanitize_filenames', 'value' => '1',      'group' => 'media'],
            ['key' => 'media_organize_by_date',   'value' => '0',      'group' => 'media'],
        ];

        foreach ($defaults as $option) {
            DB::table('options')->insertOrIgnore($option);
        }
    }

    public function down(): void
    {
        DB::table('options')->where('group', 'media')->delete();
    }
};