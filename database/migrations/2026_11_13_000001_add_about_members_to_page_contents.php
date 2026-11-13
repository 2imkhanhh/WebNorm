<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\PageContent;

return new class extends Migration
{
    public function up(): void
    {
        $fields = [
            [
                'field_key' => 'members_heading_1',
                'field_label' => 'Tiêu đề dòng 1 (trái)',
                'field_type' => 'text',
                'value' => [
                    'vi' => 'GẶP GỠ',
                    'en' => 'MEET',
                ],
                'sort_order' => 23,
            ],
            [
                'field_key' => 'members_heading_2',
                'field_label' => 'Tiêu đề dòng 2 (phải)',
                'field_type' => 'text',
                'value' => [
                    'vi' => 'THÀNH VIÊN',
                    'en' => 'THE TEAM',
                ],
                'sort_order' => 24,
            ],
            [
                'field_key' => 'members_list',
                'field_label' => 'Danh sách thành viên',
                'field_type' => 'repeater',
                'value' => [
                    [
                        'id' => 1,
                        'name' => ['vi' => 'Mạnh Thọ', 'en' => 'Manh Tho'],
                        'role' => ['vi' => 'CEO - CO founder', 'en' => 'CEO - CO founder'],
                        'image' => '/assets/images/team1.png',
                    ],
                    [
                        'id' => 2,
                        'name' => ['vi' => 'Loan Ng', 'en' => 'Loan Ng'],
                        'role' => ['vi' => 'Marketing Executive', 'en' => 'Marketing Executive'],
                        'image' => '/assets/images/team2.png',
                    ],
                    [
                        'id' => 3,
                        'name' => ['vi' => 'Yen Nguyen', 'en' => 'Yen Nguyen'],
                        'role' => ['vi' => 'UI/UX designer', 'en' => 'UI/UX designer'],
                        'image' => '/assets/images/team3.png',
                    ],
                    [
                        'id' => 4,
                        'name' => ['vi' => 'Duw', 'en' => 'Duw'],
                        'role' => ['vi' => 'Junior designer', 'en' => 'Junior designer'],
                        'image' => '/assets/images/team4.png',
                    ],
                    [
                        'id' => 5,
                        'name' => ['vi' => 'Yen Nguyen', 'en' => 'Yen Nguyen'],
                        'role' => ['vi' => 'UI/UX designer', 'en' => 'UI/UX designer'],
                        'image' => '/assets/images/team3.png',
                    ],
                    [
                        'id' => 6,
                        'name' => ['vi' => 'Mạnh Thọ', 'en' => 'Manh Tho'],
                        'role' => ['vi' => 'CEO - CO founder', 'en' => 'CEO - CO founder'],
                        'image' => '/assets/images/team1.png',
                    ],
                    [
                        'id' => 7,
                        'name' => ['vi' => 'Loan Ng', 'en' => 'Loan Ng'],
                        'role' => ['vi' => 'Marketing Executive', 'en' => 'Marketing Executive'],
                        'image' => '/assets/images/team2.png',
                    ],
                ],
                'sort_order' => 25,
            ],
        ];

        foreach ($fields as $f) {
            PageContent::updateOrCreate(
                [
                    'page_slug' => '/about-us',
                    'section_id' => 'about_members',
                    'field_key' => $f['field_key'],
                ],
                [
                    'section_title' => 'Gặp gỡ thành viên',
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
            ->where('section_id', 'about_members')
            ->delete();
    }
};

