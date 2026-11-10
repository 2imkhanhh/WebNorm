<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\PageContent;

return new class extends Migration
{
    public function up(): void
    {
        // Adjust existing sort orders to keep clear chronological ordering
        PageContent::where('page_slug', '/')->where('section_id', 'showcase')->update(['sort_order' => 11]);
        PageContent::where('page_slug', '/')->where('section_id', 'testimonials')->update(['sort_order' => 12]);
        PageContent::where('page_slug', '/')->where('section_id', 'footer_words')->where('field_key', 'footer_line1_old')->update(['sort_order' => 13]);
        PageContent::where('page_slug', '/')->where('section_id', 'footer_words')->where('field_key', 'footer_line1_new')->update(['sort_order' => 14]);
        PageContent::where('page_slug', '/')->where('section_id', 'footer_words')->where('field_key', 'footer_line2_old')->update(['sort_order' => 15]);
        PageContent::where('page_slug', '/')->where('section_id', 'footer_words')->where('field_key', 'footer_line2_new')->update(['sort_order' => 16]);
        PageContent::where('page_slug', '/')->where('section_id', 'footer_words')->where('field_key', 'footer_line3_old')->update(['sort_order' => 17]);
        PageContent::where('page_slug', '/')->where('section_id', 'footer_words')->where('field_key', 'footer_line3_new')->update(['sort_order' => 18]);

        // Insert or update intro_about fields
        $fields = [
            [
                'field_key' => 'about_label',
                'field_label' => 'Nhãn tiêu đề (Label)',
                'field_type' => 'text',
                'value' => [
                    'vi' => '• Về chúng tôi',
                    'en' => '• About us',
                ],
                'sort_order' => 7,
            ],
            [
                'field_key' => 'about_desc',
                'field_label' => 'Nội dung mô tả',
                'field_type' => 'textarea',
                'value' => [
                    'vi' => 'Có rất nhiều lý do tại sao xây dựng thương hiệu và tiếp thị lại quan trọng. Vô số sách viết về chủ đề này, nhưng tất cả đều quy về một điều đơn giản: tại sao mọi người nên quan tâm đến sự tồn tại của bạn? Chúng tôi tạo ra những trải nghiệm khiến mọi người chú ý và quan tâm.',
                    'en' => 'There are countless reasons why branding and marketing matter. Endless books have been written on this topic, but it all comes down to one simple question: why should anyone care about your existence? We create experiences that make people notice and care.',
                ],
                'sort_order' => 8,
            ],
            [
                'field_key' => 'about_btn_text',
                'field_label' => 'Chữ trên nút bấm',
                'field_type' => 'text',
                'value' => [
                    'vi' => 'ĐỌC THÊM',
                    'en' => 'READ MORE',
                ],
                'sort_order' => 9,
            ],
            [
                'field_key' => 'about_btn_url',
                'field_label' => 'Đường dẫn liên kết nút',
                'field_type' => 'link',
                'value' => [
                    'vi' => '/about-us',
                    'en' => '/about-us',
                ],
                'sort_order' => 10,
            ],
        ];

        foreach ($fields as $f) {
            PageContent::updateOrCreate(
                [
                    'page_slug' => '/',
                    'section_id' => 'intro_about',
                    'field_key' => $f['field_key'],
                ],
                [
                    'section_title' => 'Khối Giới thiệu (Về chúng tôi)',
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
            ->where('section_id', 'intro_about')
            ->delete();
    }
};

