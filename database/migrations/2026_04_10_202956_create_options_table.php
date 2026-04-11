<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->timestamps();
        });

        // Дефолтные значения
        DB::table('options')->insert([
            [
                'key'   => 'admin_path',
                'value' => 'magbusjapbozheslav',
                'group' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'   => 'site_name',
                'value' => 'OnFlaude CMS',
                'group' => 'general',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('options');
    }
};