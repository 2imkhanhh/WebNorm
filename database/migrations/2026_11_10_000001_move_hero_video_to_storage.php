<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\PageContent;
use App\Models\Media;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update page_contents hero_video path to storage
        $content = PageContent::where('page_slug', '/')
            ->where('section_id', 'hero')
            ->where('field_key', 'hero_video')
            ->first();

        if ($content) {
            $content->update([
                'value' => [
                    'vi' => 'storage/uploads/main-feature.mp4',
                    'en' => 'storage/uploads/main-feature.mp4',
                ],
                'draft_value' => null, // reset draft or align with storage
            ]);
        }

        // 2. Register into media table if not exists
        Media::firstOrCreate(
            ['file_path' => '/storage/uploads/main-feature.mp4'],
            [
                'name' => 'main-feature.mp4',
                'file_type' => 'video',
                'file_size' => 8016525,
            ]
        );
    }

    public function down(): void
    {
        $content = PageContent::where('page_slug', '/')
            ->where('section_id', 'hero')
            ->where('field_key', 'hero_video')
            ->first();

        if ($content) {
            $content->update([
                'value' => [
                    'vi' => 'assets/images/main-feature.mp4',
                    'en' => 'assets/images/main-feature.mp4',
                ],
            ]);
        }
    }
};

