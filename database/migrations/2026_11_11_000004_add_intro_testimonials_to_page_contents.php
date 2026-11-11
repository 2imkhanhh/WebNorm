<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\PageContent;

return new class extends Migration
{
    public function up(): void
    {
        $fields = [
            [
                'field_key' => 'testimonial_title_1',
                'field_label' => 'Dòng tiêu đề 1 (chữ sau dấu “)',
                'field_type' => 'text',
                'value' => [
                    'vi' => 'TRẢI NGHIỆM',
                    'en' => 'CLIENT',
                ],
                'sort_order' => 15,
            ],
            [
                'field_key' => 'testimonial_title_2',
                'field_label' => 'Dòng tiêu đề 2 (bên phải)',
                'field_type' => 'text',
                'value' => [
                    'vi' => 'KHÁCH HÀNG',
                    'en' => 'EXPERIENCES',
                ],
                'sort_order' => 16,
            ],
        ];

        foreach ($fields as $f) {
            PageContent::updateOrCreate(
                [
                    'page_slug' => '/',
                    'section_id' => 'intro_testimonials',
                    'field_key' => $f['field_key'],
                ],
                [
                    'section_title' => 'Khối Trải nghiệm khách hàng',
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
            ->where('section_id', 'intro_testimonials')
            ->delete();
    }
};

