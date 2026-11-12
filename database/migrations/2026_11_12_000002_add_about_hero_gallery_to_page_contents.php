<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\PageContent;

return new class extends Migration
{
    public function up(): void
    {
        PageContent::updateOrCreate(
            [
                'page_slug' => '/about-us',
                'section_id' => 'about_hero_gallery',
                'field_key' => 'hero_gallery',
            ],
            [
                'section_title' => 'Bộ sưu tập ảnh chuyển động đầu trang',
                'field_label' => 'Danh sách ảnh chuyển động',
                'field_type' => 'gallery',
                'value' => [
                    '/assets/images/gallery1.jpg',
                    '/assets/images/gallery2.jpg',
                    '/assets/images/gallery3.jpg',
                    '/assets/images/gallery4.jpg',
                    '/assets/images/gallery5.jpg',
                    '/assets/images/gallery6.jpg',
                    '/assets/images/gallery7.jpg',
                    '/assets/images/gallery8.jpg',
                    '/assets/images/gallery9.jpg',
                ],
                'draft_value' => null,
                'draft_updated_at' => null,
                'sort_order' => 10,
            ]
        );
    }

    public function down(): void
    {
        PageContent::where('page_slug', '/about-us')
            ->where('section_id', 'about_hero_gallery')
            ->where('field_key', 'hero_gallery')
            ->delete();
    }
};

