<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\PageContent;

return new class extends Migration
{
    public function up(): void
    {
        $fields = [
            [
                'field_key' => 'workflow_label',
                'field_label' => 'Nhãn tiêu đề',
                'field_type' => 'text',
                'value' => [
                    'vi' => '• Quy trình làm việc',
                    'en' => '• Workflow',
                ],
                'sort_order' => 20,
            ],
            [
                'field_key' => 'workflow_slogan',
                'field_label' => 'Mô tả / Slogan',
                'field_type' => 'textarea',
                'value' => [
                    'vi' => 'Chúng tôi tạo ra những không gian sáng tạo và giàu tính gợi mở, thúc đẩy những trải nghiệm sâu sắc hơn của con người.',
                    'en' => 'We create innovative and thought-provoking environments that foster deeper human experiences.',
                ],
                'sort_order' => 21,
            ],
            [
                'field_key' => 'step1_title',
                'field_label' => 'Bước 1: Tiêu đề',
                'field_type' => 'text',
                'value' => [
                    'vi' => 'Trao đổi & tìm hiểu',
                    'en' => 'Discovery & Consultation',
                ],
                'sort_order' => 22,
            ],
            [
                'field_key' => 'step1_desc',
                'field_label' => 'Bước 1: Mô tả',
                'field_type' => 'textarea',
                'value' => [
                    'vi' => 'Tổ chức buổi trao đổi trực tuyến để thu thập thông tin chuyên sâu về thương hiệu, xác định tầm nhìn và kỳ vọng chung.',
                    'en' => 'Organize an online session to gather in-depth brand insights, defining shared vision and expectations.',
                ],
                'sort_order' => 23,
            ],
            [
                'field_key' => 'step1_image',
                'field_label' => 'Bước 1: Hình ảnh',
                'field_type' => 'image',
                'value' => [
                    'vi' => '/assets/images/gallery1.jpg',
                    'en' => '/assets/images/gallery1.jpg',
                ],
                'sort_order' => 24,
            ],
            [
                'field_key' => 'step2_title',
                'field_label' => 'Bước 2: Tiêu đề',
                'field_type' => 'text',
                'value' => [
                    'vi' => 'Khởi tạo & ký kết',
                    'en' => 'Initiation & Agreement',
                ],
                'sort_order' => 25,
            ],
            [
                'field_key' => 'step2_desc',
                'field_label' => 'Bước 2: Mô tả',
                'field_type' => 'textarea',
                'value' => [
                    'vi' => 'Thống nhất phương án hợp tác, ký kết hợp đồng và xác lập các mốc thời gian triển khai cụ thể.',
                    'en' => 'Finalize the scope of collaboration, sign the contract, and establish clear delivery milestones.',
                ],
                'sort_order' => 26,
            ],
            [
                'field_key' => 'step2_image',
                'field_label' => 'Bước 2: Hình ảnh',
                'field_type' => 'image',
                'value' => [
                    'vi' => '/assets/images/gallery2.jpg',
                    'en' => '/assets/images/gallery2.jpg',
                ],
                'sort_order' => 27,
            ],
            [
                'field_key' => 'step3_title',
                'field_label' => 'Bước 3: Tiêu đề',
                'field_type' => 'text',
                'value' => [
                    'vi' => 'Thiết kế & hoàn thiện',
                    'en' => 'Design & Execution',
                ],
                'sort_order' => 28,
            ],
            [
                'field_key' => 'step3_desc',
                'field_label' => 'Bước 3: Mô tả',
                'field_type' => 'textarea',
                'value' => [
                    'vi' => 'Triển khai thiết kế dựa trên các định hướng đã thống nhất và hoàn thiện sản phẩm cuối cùng.',
                    'en' => 'Execute creative design based on agreed directions and deliver the finalized outputs.',
                ],
                'sort_order' => 29,
            ],
            [
                'field_key' => 'step3_image',
                'field_label' => 'Bước 3: Hình ảnh',
                'field_type' => 'image',
                'value' => [
                    'vi' => '/assets/images/gallery3.jpg',
                    'en' => '/assets/images/gallery3.jpg',
                ],
                'sort_order' => 30,
            ],
        ];

        foreach ($fields as $f) {
            PageContent::updateOrCreate(
                [
                    'page_slug' => '/about-us',
                    'section_id' => 'about_workflow',
                    'field_key' => $f['field_key'],
                ],
                [
                    'section_title' => 'Quy trình làm việc (Workflow)',
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
            ->where('section_id', 'about_workflow')
            ->whereNotIn('field_key', ['step1_title', 'step2_title', 'step3_title'])
            ->delete();
    }
};

