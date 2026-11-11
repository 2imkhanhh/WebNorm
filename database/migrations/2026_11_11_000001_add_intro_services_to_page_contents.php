<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\PageContent;

return new class extends Migration
{
    public function up(): void
    {
        $fields = [
            [
                'field_key' => 'services_label',
                'field_label' => 'Nhãn tiêu đề (Label)',
                'field_type' => 'text',
                'value' => [
                    'vi' => '• Dịch vụ',
                    'en' => '• Services',
                ],
                'sort_order' => 12,
            ],
            [
                'field_key' => 'services_statement',
                'field_label' => 'Tuyên ngôn dịch vụ (Statement)',
                'field_type' => 'textarea',
                'value' => [
                    'vi' => 'Chúng tôi giúp doanh nghiệp xây dựng thương hiệu rõ ràng, nhất quán để truyền tải giá trị khác biệt và tăng trưởng.',
                    'en' => 'We help businesses build clear, consistent brands to convey distinct value and drive growth.',
                ],
                'sort_order' => 13,
            ],
            [
                'field_key' => 'services_desc',
                'field_label' => 'Mô tả ngắn bên phải',
                'field_type' => 'textarea',
                'value' => [
                    'vi' => 'Từ chiến lược đến trải nghiệm, chúng tôi định hình thương hiệu rõ nét.',
                    'en' => 'From strategy to experience, we shape distinct brands.',
                ],
                'sort_order' => 14,
            ],
        ];

        foreach ($fields as $f) {
            PageContent::updateOrCreate(
                [
                    'page_slug' => '/',
                    'section_id' => 'intro_services',
                    'field_key' => $f['field_key'],
                ],
                [
                    'section_title' => 'Khối Giới thiệu Dịch vụ',
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
            ->where('section_id', 'intro_services')
            ->delete();
    }
};

