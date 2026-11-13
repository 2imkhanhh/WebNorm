<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\PageContent;

return new class extends Migration
{
    public function up(): void
    {
        PageContent::where('page_slug', '/work')
            ->where('section_id', 'work_hero')
            ->whereIn('field_key', ['filter_identity', 'filter_website'])
            ->delete();

        PageContent::where('page_slug', '/work')
            ->where('section_id', 'work_hero')
            ->where('field_key', 'filter_all')
            ->update(['field_label' => 'Nhãn nút "Tất cả dự án"']);

        PageContent::where('page_slug', '/work')
            ->where('section_id', 'work_hero')
            ->where('field_key', 'filter_featured')
            ->update(['field_label' => 'Nhãn nút "Nổi bật"']);
    }

    public function down(): void
    {
        PageContent::updateOrCreate(
            [
                'page_slug' => '/work',
                'section_id' => 'work_hero',
                'field_key' => 'filter_identity',
            ],
            [
                'section_title' => 'Banner Dự án & Bộ lọc',
                'field_label' => 'Nút bộ lọc 3',
                'field_type' => 'text',
                'value' => ['vi' => 'NHẬN DIỆN', 'en' => 'IDENTITY'],
                'sort_order' => 19,
            ]
        );

        PageContent::updateOrCreate(
            [
                'page_slug' => '/work',
                'section_id' => 'work_hero',
                'field_key' => 'filter_website',
            ],
            [
                'section_title' => 'Banner Dự án & Bộ lọc',
                'field_label' => 'Nút bộ lọc 4',
                'field_type' => 'text',
                'value' => ['vi' => 'WEBSITE', 'en' => 'WEBSITE'],
                'sort_order' => 20,
            ]
        );
    }
};

