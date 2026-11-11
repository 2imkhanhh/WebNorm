<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\PageContent;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Shift footer_words sort_order up to 25-30 to guarantee it stays after intro_blog
        $footerKeys = [
            'footer_line1_old' => 25,
            'footer_line1_new' => 26,
            'footer_line2_old' => 27,
            'footer_line2_new' => 28,
            'footer_line3_old' => 29,
            'footer_line3_new' => 30,
        ];
        foreach ($footerKeys as $key => $sort) {
            PageContent::where('page_slug', '/')
                ->where('section_id', 'footer_words')
                ->where('field_key', $key)
                ->update(['sort_order' => $sort]);
        }

        // 2. Insert fields for intro_blog (sort_order 17 - 20)
        $fields = [
            [
                'field_key' => 'blog_label',
                'field_label' => 'Nhãn tiêu đề (chữ sau dấu •)',
                'field_type' => 'text',
                'value' => [
                    'vi' => 'Blog',
                    'en' => 'Blog',
                ],
                'sort_order' => 17,
            ],
            [
                'field_key' => 'blog_statement',
                'field_label' => 'Tuyên ngôn bài viết (Statement)',
                'field_type' => 'textarea',
                'value' => [
                    'vi' => 'Chia sẻ góc nhìn về thương hiệu, thiết kế và chiến lược giúp doanh nghiệp phát triển bền vững.',
                    'en' => 'Sharing perspectives on branding, design, and strategy to help businesses grow sustainably.',
                ],
                'sort_order' => 18,
            ],
            [
                'field_key' => 'blog_btn_text',
                'field_label' => 'Chữ trên nút xem thêm',
                'field_type' => 'text',
                'value' => [
                    'vi' => 'XEM THÊM BÀI VIẾT',
                    'en' => 'VIEW ALL ARTICLES',
                ],
                'sort_order' => 19,
            ],
            [
                'field_key' => 'blog_btn_url',
                'field_label' => 'Đường dẫn liên kết nút',
                'field_type' => 'link',
                'value' => [
                    'vi' => '/news',
                    'en' => '/news',
                ],
                'sort_order' => 20,
            ],
        ];

        foreach ($fields as $f) {
            PageContent::updateOrCreate(
                [
                    'page_slug' => '/',
                    'section_id' => 'intro_blog',
                    'field_key' => $f['field_key'],
                ],
                [
                    'section_title' => 'Khối Giới thiệu Blog',
                    'field_label' => $f['field_label'],
                    'field_type' => $f['field_type'],
                    'value' => $f['value'],
                    'sort_order' => $f['sort_order'],
                ]
            );
        }
    }

    public function down(): void
    {
        PageContent::where('page_slug', '/')
            ->where('section_id', 'intro_blog')
            ->delete();
    }
};

