<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\PageContent;

return new class extends Migration
{
    public function up(): void
    {
        // Re-order workflow steps to leave space for about_slogan_old and about_slogan_new
        PageContent::where('page_slug', '/about-us')->where('section_id', 'about_workflow')->where('field_key', 'step1_title')->update(['sort_order' => 16]);
        PageContent::where('page_slug', '/about-us')->where('section_id', 'about_workflow')->where('field_key', 'step2_title')->update(['sort_order' => 17]);
        PageContent::where('page_slug', '/about-us')->where('section_id', 'about_workflow')->where('field_key', 'step3_title')->update(['sort_order' => 18]);

        // Delete old single about_slogan
        PageContent::where('page_slug', '/about-us')
            ->where('section_id', 'about_intro')
            ->where('field_key', 'about_slogan')
            ->delete();

        // Create about_slogan_old
        PageContent::updateOrCreate(
            [
                'page_slug' => '/about-us',
                'section_id' => 'about_intro',
                'field_key' => 'about_slogan_old',
            ],
            [
                'section_title' => 'Giới thiệu & Giải pháp',
                'field_label' => 'Slogan dòng 3 - Chữ ban đầu',
                'field_type' => 'text',
                'value' => [
                    'vi' => 'THƯƠNG HIỆU',
                    'en' => 'ENTERPRISE',
                ],
                'draft_value' => null,
                'draft_updated_at' => null,
                'sort_order' => 14,
            ]
        );

        // Create about_slogan_new
        PageContent::updateOrCreate(
            [
                'page_slug' => '/about-us',
                'section_id' => 'about_intro',
                'field_key' => 'about_slogan_new',
            ],
            [
                'section_title' => 'Giới thiệu & Giải pháp',
                'field_label' => 'Slogan dòng 3 - Chữ biến đổi',
                'field_type' => 'text',
                'value' => [
                    'vi' => 'DOANH NGHIỆP',
                    'en' => 'BRANDS',
                ],
                'draft_value' => null,
                'draft_updated_at' => null,
                'sort_order' => 15,
            ]
        );
    }

    public function down(): void
    {
        PageContent::where('page_slug', '/about-us')
            ->where('section_id', 'about_intro')
            ->whereIn('field_key', ['about_slogan_old', 'about_slogan_new'])
            ->delete();

        PageContent::updateOrCreate(
            [
                'page_slug' => '/about-us',
                'section_id' => 'about_intro',
                'field_key' => 'about_slogan',
            ],
            [
                'section_title' => 'Giới thiệu & Giải pháp',
                'field_label' => 'Slogan biến đổi',
                'field_type' => 'text',
                'value' => [
                    'vi' => 'THƯƠNG HIỆU DOANH NGHIỆP',
                    'en' => 'ENTERPRISE BRANDS',
                ],
                'sort_order' => 13,
            ]
        );
    }
};

