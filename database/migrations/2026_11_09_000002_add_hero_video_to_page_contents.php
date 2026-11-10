<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\PageContent;

return new class extends Migration
{
    public function up(): void
    {
        PageContent::updateOrCreate(
            [
                'page_slug' => '/',
                'section_id' => 'hero',
                'field_key' => 'hero_video',
            ],
            [
                'section_title' => 'Hero & Tiêu đề chính',
                'field_label' => 'Video giới thiệu (MP4 URL)',
                'field_type' => 'video',
                'value' => [
                    'vi' => 'assets/images/main-feature.mp4',
                    'en' => 'assets/images/main-feature.mp4',
                ],
                'sort_order' => 6,
            ]
        );
    }

    public function down(): void
    {
        PageContent::where('page_slug', '/')
            ->where('section_id', 'hero')
            ->where('field_key', 'hero_video')
            ->delete();
    }
};

