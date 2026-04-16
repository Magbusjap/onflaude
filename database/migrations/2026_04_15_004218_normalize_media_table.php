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
        Schema::table('media', function (Blueprint $table) {
            $table->renameColumn('name', 'original_name');
            $table->renameColumn('file_name', 'filename');
            $table->renameColumn('alt', 'alt_text');
            $table->dropColumn(['disk', 'title', 'caption', 'folder']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->renameColumn('original_name', 'name');
            $table->renameColumn('filename', 'file_name');
            $table->renameColumn('alt_text', 'alt');
            $table->string('disk')->default('public');
            $table->string('title')->nullable();
            $table->text('caption')->nullable();
            $table->string('folder')->default('/');
        });
    }
};
