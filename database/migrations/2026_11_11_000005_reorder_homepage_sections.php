<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\PageContent;

return new class extends Migration
{
    public function up(): void
    {
        $footerSortOrders = [
            'footer_line1_old' => 20,
            'footer_line1_new' => 21,
            'footer_line2_old' => 22,
            'footer_line2_new' => 23,
            'footer_line3_old' => 24,
            'footer_line3_new' => 25,
        ];

        foreach ($footerSortOrders as $key => $sort) {
            PageContent::where('page_slug', '/')
                ->where('section_id', 'footer_words')
                ->where('field_key', $key)
                ->update(['sort_order' => $sort]);
        }

        PageContent::where('page_slug', '/')
            ->where('section_id', 'intro_testimonials')
            ->where('field_key', 'testimonial_title_1')
            ->update(['sort_order' => 15]);

        PageContent::where('page_slug', '/')
            ->where('section_id', 'intro_testimonials')
            ->where('field_key', 'testimonial_title_2')
            ->update(['sort_order' => 16]);
    }

    public function down(): void
    {
        //
    }
};

