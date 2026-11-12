<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\PageContent;

return new class extends Migration
{
    public function up(): void
    {
        // Re-order workflow steps to leave space for about_story
        PageContent::where('page_slug', '/about-us')->where('section_id', 'about_workflow')->where('field_key', 'step1_title')->update(['sort_order' => 20]);
        PageContent::where('page_slug', '/about-us')->where('section_id', 'about_workflow')->where('field_key', 'step2_title')->update(['sort_order' => 21]);
        PageContent::where('page_slug', '/about-us')->where('section_id', 'about_workflow')->where('field_key', 'step3_title')->update(['sort_order' => 22]);

        $fields = [
            [
                'field_key' => 'story_label',
                'field_label' => 'Nhãn tiêu đề',
                'field_type' => 'text',
                'value' => [
                    'vi' => '• Về chúng tôi',
                    'en' => '• About us',
                ],
                'sort_order' => 16,
            ],
            [
                'field_key' => 'story_desc_left',
                'field_label' => 'Nội dung đoạn 1 (trái)',
                'field_type' => 'textarea',
                'value' => [
                    'vi' => 'NÔRM Creative là agency sáng tạo thế hệ mới, chuyên sâu trong lĩnh vực thiết kế nhận diện thương hiệu, tư vấn chiến lược bài bản, toàn diện và các giải pháp hình ảnh chuyên nghiệp cho doanh nghiệp.',
                    'en' => 'NÔRM Creative is a next-generation creative agency specializing in brand identity design, comprehensive strategy consulting, and professional visual solutions for businesses.',
                ],
                'sort_order' => 17,
            ],
            [
                'field_key' => 'story_desc_right',
                'field_label' => 'Nội dung đoạn 2 (phải)',
                'field_type' => 'textarea',
                'value' => [
                    'vi' => 'NÔRM Creative ra đời với một tinh thần mới mẻ, hiện đại, trẻ trung và phá cách. Lấy văn hoá làm cốt lõi, kết hợp cùng tư duy thiết kế mở, chúng tôi đưa câu chuyện và cá tính thương hiệu vào từng sản phẩm thiết kế, từ đó thổi hồn, kiến tạo bản sắc và khắc họa tính cách thương hiệu một cách rõ nét.',
                    'en' => 'NÔRM Creative was born with a fresh, modern, youthful, and disruptive spirit. Taking culture as the core combined with open design thinking, we weave brand stories and personality into every design product, breathing life, forging identity, and distinctly portraying brand character.',
                ],
                'sort_order' => 18,
            ],
            [
                'field_key' => 'story_image',
                'field_label' => 'Hình ảnh giới thiệu',
                'field_type' => 'image',
                'value' => [
                    'vi' => '/assets/images/team-photo.jpg',
                    'en' => '/assets/images/team-photo.jpg',
                ],
                'sort_order' => 19,
            ],
        ];

        foreach ($fields as $f) {
            PageContent::updateOrCreate(
                [
                    'page_slug' => '/about-us',
                    'section_id' => 'about_story',
                    'field_key' => $f['field_key'],
                ],
                [
                    'section_title' => 'Về chúng tôi',
                    'field_label' => $f['field_label'],
                    'field_type' => $f['field_type'],
                    'value' => $f['value'],
                    'draft_value' => null,
                    'draft_updated_at' => null,
                    'sort_order' => $f['sort_order'],
                ]
            );
        }
    }

    public function down(): void
    {
        PageContent::where('page_slug', '/about-us')
            ->where('section_id', 'about_story')
            ->delete();
    }
};

