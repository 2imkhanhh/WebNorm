<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageContent extends Model
{
    protected $fillable = [
        'page_slug',
        'section_id',
        'section_title',
        'field_key',
        'field_label',
        'field_type',
        'value',
        'sort_order',
    ];

    protected $casts = [
        'value' => 'json',
    ];

    /**
     * Helper to get a field value in specific language
     */
    public function getTranslated(string $lang = 'vi')
    {
        if (is_array($this->value)) {
            return $this->value[$lang] ?? $this->value['vi'] ?? '';
        }
        return $this->value ?? '';
    }
}

