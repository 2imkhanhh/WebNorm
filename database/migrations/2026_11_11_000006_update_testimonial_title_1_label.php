<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\PageContent;

return new class extends Migration
{
    public function up(): void
    {
        PageContent::where('field_key', 'testimonial_title_1')
            ->update([
                'field_label' => 'Dòng tiêu đề 1 (bên trái)',
            ]);
    }

    public function down(): void
    {
        //
    }
};

