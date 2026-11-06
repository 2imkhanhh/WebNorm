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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->json('title'); // {"vi": "...", "en": "..."}
            $table->string('slug')->unique();
            $table->json('category')->nullable();
            $table->string('thumbnail')->nullable();
            $table->json('excerpt')->nullable();
            $table->json('content')->nullable();
            $table->string('author')->nullable()->default('Norm');
            $table->dateTime('published_at')->nullable();
            $table->string('status')->default('published'); // 'draft', 'published'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};

