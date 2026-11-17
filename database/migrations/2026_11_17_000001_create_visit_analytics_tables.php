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
        if (! Schema::hasTable('daily_site_visits')) {
            Schema::create('daily_site_visits', function (Blueprint $table) {
                $table->id();
                $table->date('visit_date')->unique();
                $table->unsignedBigInteger('views')->default(0);
                $table->unsignedBigInteger('unique_visitors')->default(0);
                $table->unsignedBigInteger('vi_views')->default(0);
                $table->unsignedBigInteger('en_views')->default(0);
                $table->unsignedBigInteger('desktop_views')->default(0);
                $table->unsignedBigInteger('mobile_views')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('daily_page_views')) {
            Schema::create('daily_page_views', function (Blueprint $table) {
                $table->id();
                $table->date('visit_date');
                $table->char('path_hash', 32);
                $table->string('page_path', 191);
                $table->string('page_group', 32)->default('other');
                $table->unsignedBigInteger('views')->default(0);
                $table->timestamps();

                $table->unique(['visit_date', 'path_hash'], 'daily_page_views_date_hash_unique');
                $table->index('visit_date');
            });
        }

        if (! Schema::hasTable('daily_visitor_hashes')) {
            Schema::create('daily_visitor_hashes', function (Blueprint $table) {
                $table->id();
                $table->date('visit_date');
                $table->char('visitor_hash', 64);
                $table->timestamp('created_at')->nullable();

                $table->unique(['visit_date', 'visitor_hash'], 'daily_visitor_hashes_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_visitor_hashes');
        Schema::dropIfExists('daily_page_views');
        Schema::dropIfExists('daily_site_visits');
    }
};

