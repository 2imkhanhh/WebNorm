<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\PageContent;

class RemoveShowcaseAndTestimonialsFromPageContents extends Migration
{
    public function up(): void
    {
        PageContent::where('page_slug', '/')
            ->whereIn('section_id', ['showcase', 'testimonials'])
            ->delete();
    }

    public function down(): void
    {
        //
    }
}

