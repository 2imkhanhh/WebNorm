<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Blog;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            if (!Schema::hasColumn('blogs', 'author_role')) {
                $table->json('author_role')->nullable()->after('author');
            }
            if (!Schema::hasColumn('blogs', 'author_avatar')) {
                $table->string('author_avatar')->nullable()->after('author_role');
            }
        });

        // Seed author and role from static template for sample blogs
        Blog::where('slug', 'hieu-ve-tu-duy-thiet-ke-design-thinking')
            ->update([
                'author' => 'Nguyễn Minh Anh',
                'author_role' => ['vi' => 'Giám đốc Marketing', 'en' => 'Marketing Director'],
                'author_avatar' => '/assets/images/avatar.png',
            ]);

        Blog::where('slug', 'tai-dinh-vi-thuong-hieu-ky-nguyen-so')
            ->update([
                'author' => 'Nguyễn Minh Anh',
                'author_role' => ['vi' => 'Giám đốc Chiến lược', 'en' => 'Strategy Director'],
                'author_avatar' => '/assets/images/avatar.png',
            ]);

        Blog::where('slug', 'thiet-ke-toi-gian-va-suc-hut-thuong-hieu')
            ->update([
                'author' => 'Nguyễn Minh Anh',
                'author_role' => ['vi' => 'Giám đốc Sáng tạo', 'en' => 'Creative Director'],
                'author_avatar' => '/assets/images/avatar.png',
            ]);
    }

    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            if (Schema::hasColumn('blogs', 'author_avatar')) {
                $table->dropColumn('author_avatar');
            }
            if (Schema::hasColumn('blogs', 'author_role')) {
                $table->dropColumn('author_role');
            }
        });
    }
};

