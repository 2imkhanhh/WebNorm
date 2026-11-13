<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\PageContent;

return new class extends Migration
{
    public function up(): void
    {
        PageContent::where('page_slug', '/about-us')
            ->where('section_id', 'about_members')
            ->update(['section_title' => 'Thành viên']);

        PageContent::where('page_slug', '/about-us')
            ->where('section_id', 'about_members')
            ->where('field_key', 'members_heading_1')
            ->update(['field_label' => 'Tiêu đề dòng 1']);

        PageContent::where('page_slug', '/about-us')
            ->where('section_id', 'about_members')
            ->where('field_key', 'members_heading_2')
            ->update(['field_label' => 'Tiêu đề dòng 2']);
    }

    public function down(): void
    {
        PageContent::where('page_slug', '/about-us')
            ->where('section_id', 'about_members')
            ->update(['section_title' => 'Gặp gỡ thành viên']);

        PageContent::where('page_slug', '/about-us')
            ->where('section_id', 'about_members')
            ->where('field_key', 'members_heading_1')
            ->update(['field_label' => 'Tiêu đề dòng 1 (trái)']);

        PageContent::where('page_slug', '/about-us')
            ->where('section_id', 'about_members')
            ->where('field_key', 'members_heading_2')
            ->update(['field_label' => 'Tiêu đề dòng 2 (phải)']);
    }
};

