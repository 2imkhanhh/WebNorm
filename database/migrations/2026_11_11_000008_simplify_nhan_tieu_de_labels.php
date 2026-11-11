<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\PageContent;

return new class extends Migration
{
    public function up(): void
    {
        PageContent::whereIn('field_key', ['about_label', 'services_label', 'blog_label'])
            ->update(['field_label' => 'Nhãn tiêu đề']);
    }

    public function down(): void
    {
        PageContent::whereIn('field_key', ['about_label', 'services_label', 'blog_label'])
            ->update(['field_label' => 'Nhãn tiêu đề (chữ sau dấu •)']);
    }
};

