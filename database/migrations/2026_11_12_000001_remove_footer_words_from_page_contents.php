<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\PageContent;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Delete footer words from page_contents table because they are now globally managed in settings
        PageContent::where('section_id', 'footer_words')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $footerDefaults = [
            [
                'page_slug' => '/',
                'section_id' => 'footer_words',
                'section_title' => 'Chữ lật chân trang (Footer Animation)',
                'field_key' => 'footer_line1_old',
                'field_label' => 'Dòng 1 (Trước)',
                'field_type' => 'text',
                'value' => ['vi' => 'TỪ NHỮNG', 'en' => 'FROM AN'],
                'sort_order' => 25,
            ],
            [
                'page_slug' => '/',
                'section_id' => 'footer_words',
                'section_title' => 'Chữ lật chân trang (Footer Animation)',
                'field_key' => 'footer_line1_new',
                'field_label' => 'Dòng 1 (Sau)',
                'field_type' => 'text',
                'value' => ['vi' => 'ĐẾN MỘT', 'en' => 'INTO A'],
                'sort_order' => 26,
            ],
            [
                'page_slug' => '/',
                'section_id' => 'footer_words',
                'section_title' => 'Chữ lật chân trang (Footer Animation)',
                'field_key' => 'footer_line2_old',
                'field_label' => 'Dòng 2 (Trước)',
                'field_type' => 'text',
                'value' => ['vi' => 'Ý TƯỞNG', 'en' => 'IDEA'],
                'sort_order' => 27,
            ],
            [
                'page_slug' => '/',
                'section_id' => 'footer_words',
                'section_title' => 'Chữ lật chân trang (Footer Animation)',
                'field_key' => 'footer_line2_new',
                'field_label' => 'Dòng 2 (Sau)',
                'field_type' => 'text',
                'value' => ['vi' => 'THƯƠNG HIỆU', 'en' => 'BRAND'],
                'sort_order' => 28,
            ],
            [
                'page_slug' => '/',
                'section_id' => 'footer_words',
                'section_title' => 'Chữ lật chân trang (Footer Animation)',
                'field_key' => 'footer_line3_old',
                'field_label' => 'Dòng 3 (Trước)',
                'field_type' => 'text',
                'value' => ['vi' => 'BAN ĐẦU', 'en' => 'INITIAL'],
                'sort_order' => 29,
            ],
            [
                'page_slug' => '/',
                'section_id' => 'footer_words',
                'section_title' => 'Chữ lật chân trang (Footer Animation)',
                'field_key' => 'footer_line3_new',
                'field_label' => 'Dòng 3 (Sau)',
                'field_type' => 'text',
                'value' => ['vi' => 'ĐÁNG NHỚ', 'en' => 'MEMORABLE'],
                'sort_order' => 30,
            ],
        ];

        foreach ($footerDefaults as $item) {
            PageContent::create($item);
        }
    }
};

