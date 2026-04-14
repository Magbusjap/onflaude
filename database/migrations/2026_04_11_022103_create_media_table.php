<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('name');              // original file name 
            $table->string('file_name');         // name stored in the filesystem
            $table->string('mime_type');
            $table->string('ext', 10)->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('ext', 10);         
            $table->string('disk')->default('public');
            $table->string('path');
            $table->unsignedBigInteger('size')->default(0);
            $table->unsignedInteger('width')->nullable(); 
            $table->unsignedInteger('height')->nullable();  
            $table->string('alt')->nullable();
            $table->string('title')->nullable();
            $table->text('caption')->nullable();
            $table->string('folder')->default('/');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};