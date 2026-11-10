<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Setting;
use App\Models\Media;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update model_3d in settings table
        Setting::set('model_3d', '/storage/uploads/norm.glb');

        // 2. Update or create in media table
        Media::updateOrCreate(
            ['name' => 'norm.glb'],
            [
                'file_path' => '/storage/uploads/norm.glb',
                'file_type' => 'model3d',
                'file_size' => 40766352,
            ]
        );
    }

    public function down(): void
    {
        Setting::set('model_3d', '/assets/images/norm.glb');
    }
};
