<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\PageContent;

return new class extends Migration
{
    public function up(): void
    {
        // Remove individual step columns to clean up
        PageContent::where('page_slug', '/about-us')
            ->where('section_id', 'about_workflow')
            ->where('field_key', 'LIKE', 'step%')
            ->delete();

        // Ensure workflow_label & workflow_slogan are set
        PageContent::updateOrCreate(
            [
                'page_slug' => '/about-us',
                'section_id' => 'about_workflow',
                'field_key' => 'workflow_label',
            ],
            [
                'section_title' => 'Quy trình làm việc (Workflow)',
                'field_label' => 'Nhãn tiêu đề',
                'field_type' => 'text',
                'value' => [
                    'vi' => '• Quy trình làm việc',
                    'en' => '• Workflow',
                ],
                'sort_order' => 20,
            ]
        );

        PageContent::updateOrCreate(
            [
                'page_slug' => '/about-us',
                'section_id' => 'about_workflow',
                'field_key' => 'workflow_slogan',
            ],
            [
                'section_title' => 'Quy trình làm việc (Workflow)',
                'field_label' => 'Mô tả / Slogan',
                'field_type' => 'textarea',
                'value' => [
                    'vi' => 'Chúng tôi tạo ra những không gian sáng tạo và giàu tính gợi mở, thúc đẩy những trải nghiệm sâu sắc hơn của con người.',
                    'en' => 'We create innovative and thought-provoking environments that foster deeper human experiences.',
                ],
                'sort_order' => 21,
            ]
        );

        // Add unified repeater field workflow_steps
        PageContent::updateOrCreate(
            [
                'page_slug' => '/about-us',
                'section_id' => 'about_workflow',
                'field_key' => 'workflow_steps',
            ],
            [
                'section_title' => 'Quy trình làm việc (Workflow)',
                'field_label' => 'Danh sách các bước quy trình',
                'field_type' => 'repeater',
                'value' => [
                    [
                        'id' => 1,
                        'title' => [
                            'vi' => 'Trao đổi & tìm hiểu',
                            'en' => 'Discovery & Consultation',
                        ],
                        'desc' => [
                            'vi' => 'Tổ chức buổi trao đổi trực tuyến để thu thập thông tin chuyên sâu về thương hiệu, xác định tầm nhìn và kỳ vọng chung.',
                            'en' => 'Organize an online session to gather in-depth brand insights, defining shared vision and expectations.',
                        ],
                        'image' => '/assets/images/gallery1.jpg',
                    ],
                    [
                        'id' => 2,
                        'title' => [
                            'vi' => 'Khởi tạo & ký kết',
                            'en' => 'Initiation & Agreement',
                        ],
                        'desc' => [
                            'vi' => 'Thống nhất phương án hợp tác, ký kết hợp đồng và xác lập các mốc thời gian triển khai cụ thể.',
                            'en' => 'Finalize the scope of collaboration, sign the contract, and establish clear delivery milestones.',
                        ],
                        'image' => '/assets/images/gallery2.jpg',
                    ],
                    [
                        'id' => 3,
                        'title' => [
                            'vi' => 'Thiết kế & hoàn thiện',
                            'en' => 'Design & Execution',
                        ],
                        'desc' => [
                            'vi' => 'Triển khai thiết kế dựa trên các định hướng đã thống nhất và hoàn thiện sản phẩm cuối cùng.',
                            'en' => 'Execute creative design based on agreed directions and deliver the finalized outputs.',
                        ],
                        'image' => '/assets/images/gallery3.jpg',
                    ],
                ],
                'draft_value' => null,
                'draft_updated_at' => null,
                'sort_order' => 22,
            ]
        );
    }

    public function down(): void
    {
        PageContent::where('page_slug', '/about-us')
            ->where('section_id', 'about_workflow')
            ->where('field_key', 'workflow_steps')
            ->delete();
    }
};

