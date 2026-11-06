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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->json('title'); // {"vi": "...", "en": "..."}
            $table->string('slug')->unique();
            $table->json('category')->nullable(); // {"vi": "Nhận diện thương hiệu", "en": "Brand identity"}
            $table->string('thumbnail')->nullable();
            $table->json('gallery')->nullable();
            $table->string('client')->nullable();
            $table->string('year')->nullable();
            $table->json('description')->nullable();
            $table->json('content')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};

