<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Setting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $footerDefaults = [
            'footer_btn_text' => [
                'value' => ['vi' => 'CÙNG BẮT ĐẦU', 'en' => 'GET STARTED'],
                'group' => 'footer',
                'description' => 'Chữ nút bấm kêu gọi hành động tại chân trang'
            ],
            'footer_social_title' => [
                'value' => ['vi' => 'Mạng xã hội', 'en' => 'Social Media'],
                'group' => 'footer',
                'description' => 'Tiêu đề cột mạng xã hội tại chân trang'
            ],
            'footer_line1_old' => [
                'value' => ['vi' => 'TỪ NHỮNG', 'en' => 'FROM AN'],
                'group' => 'footer',
                'description' => 'Dòng lật 1 (Trước)'
            ],
            'footer_line1_new' => [
                'value' => ['vi' => 'ĐẾN MỘT', 'en' => 'INTO A'],
                'group' => 'footer',
                'description' => 'Dòng lật 1 (Sau)'
            ],
            'footer_line2_old' => [
                'value' => ['vi' => 'Ý TƯỞNG', 'en' => 'IDEA'],
                'group' => 'footer',
                'description' => 'Dòng lật 2 (Trước)'
            ],
            'footer_line2_new' => [
                'value' => ['vi' => 'THƯƠNG HIỆU', 'en' => 'BRAND'],
                'group' => 'footer',
                'description' => 'Dòng lật 2 (Sau)'
            ],
            'footer_line3_old' => [
                'value' => ['vi' => 'BAN ĐẦU', 'en' => 'INITIAL'],
                'group' => 'footer',
                'description' => 'Dòng lật 3 (Trước)'
            ],
            'footer_line3_new' => [
                'value' => ['vi' => 'ĐÁNG NHỚ', 'en' => 'MEMORABLE'],
                'group' => 'footer',
                'description' => 'Dòng lật 3 (Sau)'
            ],
        ];

        foreach ($footerDefaults as $key => $data) {
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $data['value'],
                    'group' => $data['group'],
                    'description' => $data['description'],
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::whereIn('key', [
            'footer_btn_text',
            'footer_social_title',
            'footer_line1_old',
            'footer_line1_new',
            'footer_line2_old',
            'footer_line2_new',
            'footer_line3_old',
            'footer_line3_new',
        ])->delete();
    }
};

