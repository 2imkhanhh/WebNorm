<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\PageContent;

class CleanAboutAndServicesLabels extends Migration
{
    public function up(): void
    {
        foreach (['about_label', 'services_label'] as $key) {
            $row = PageContent::where('page_slug', '/')->where('field_key', $key)->first();
            if ($row) {
                if (is_array($row->value)) {
                    $v = $row->value;
                    $v['vi'] = trim(preg_replace('/^[•\s\-\*]+/u', '', $v['vi'] ?? ''));
                    $v['en'] = trim(preg_replace('/^[•\s\-\*]+/u', '', $v['en'] ?? ''));
                    $row->value = $v;
                }
                if ($row->draft_value && is_array($row->draft_value)) {
                    $dv = $row->draft_value;
                    $dv['vi'] = trim(preg_replace('/^[•\s\-\*]+/u', '', $dv['vi'] ?? ''));
                    $dv['en'] = trim(preg_replace('/^[•\s\-\*]+/u', '', $dv['en'] ?? ''));
                    $row->draft_value = $dv;
                }
                $row->field_label = 'Nhãn tiêu đề (chữ sau dấu •)';
                $row->save();
            }
        }
    }

    public function down(): void
    {
        //
    }
}

