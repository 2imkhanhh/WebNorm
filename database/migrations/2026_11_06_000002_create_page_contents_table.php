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
        Schema::create('page_contents', function (Blueprint $table) {
            $table->id();
            $table->string('page_slug'); // '/', '/about-us', '/work', '/news'
            $table->string('section_id'); // 'hero', 'showcase', 'testimonials', 'footer_words', etc.
            $table->string('section_title')->nullable();
            $table->string('field_key'); // 'page_title', 'hero_desc', etc.
            $table->string('field_label');
            $table->string('field_type')->default('text'); // 'text', 'textarea', 'image'
            $table->json('value')->nullable(); // {"vi": "...", "en": "..."}
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['page_slug', 'section_id', 'field_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_contents');
    }
};

