<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\PageContent;

return new class extends Migration
{
    public function up(): void
    {
        PageContent::where('page_slug', '/')
            ->where('section_id', 'hero')
            ->where('field_key', 'hero_video')
            ->update([
                'field_label' => 'Tệp giới thiệu (Video hoặc Ảnh)',
            ]);
    }

    public function down(): void
    {
        PageContent::where('page_slug', '/')
            ->where('section_id', 'hero')
            ->where('field_key', 'hero_video')
            ->update([
                'field_label' => 'Video giới thiệu (MP4 URL)',
            ]);
    }
};

