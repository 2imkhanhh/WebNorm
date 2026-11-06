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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('author'); // Tên người đánh giá
            $table->json('role')->nullable(); // {"vi": "Giám đốc Marketing", "en": "Marketing Director"}
            $table->string('avatar')->nullable();
            $table->json('content'); // {"vi": "...", "en": "..."}
            $table->tinyInteger('rating')->default(5);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};

