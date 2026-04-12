<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedTinyInteger('morning_start')->default(5);
                $table->unsignedTinyInteger('afternoon_start')->default(12);
                $table->unsignedTinyInteger('evening_start')->default(18);
                $table->unsignedTinyInteger('night_start')->default(22);
                $table->string('timezone')->default('UTC');
            });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['morning_start', 'afternoon_start', 'evening_start', 'night_start', 'timezone']);
        });
    }
};
