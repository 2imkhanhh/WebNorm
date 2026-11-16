<?php

namespace App\Models;

use App\Helpers\NormHelper;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'number',
        'title',
        'description',
        'content',
        'icon',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'title' => 'json',
        'description' => 'json',
        'content' => 'json',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getTitle(?string $lang = null): string
    {
        return NormHelper::val($this->title, '', $lang);
    }

    public function getDescription(?string $lang = null): string
    {
        return NormHelper::val($this->description, '', $lang);
    }

    public function getListItems(?string $lang = null): array
    {
        $resolvedLang = NormHelper::locale($lang);

        if (is_array($this->content)) {
            if (isset($this->content[$resolvedLang]) && is_array($this->content[$resolvedLang]) && !empty($this->content[$resolvedLang])) {
                return $this->content[$resolvedLang];
            }
            if (isset($this->content['vi']) && is_array($this->content['vi']) && !empty($this->content['vi'])) {
                return $this->content['vi'];
            }
            if (!empty($this->content) && isset($this->content[0])) {
                return $this->content;
            }
        }

        $desc = $this->getDescription($resolvedLang);
        if (!empty($desc)) {
            if (str_contains($desc, "\n")) {
                $lines = array_map('trim', explode("\n", $desc));
                return array_values(array_filter($lines, fn($l) => $l !== ''));
            }
            if (str_contains($desc, ',')) {
                $items = array_map('trim', explode(',', $desc));
                $cleaned = array_map(fn($i) => rtrim($i, '.'), $items);
                return array_values(array_filter($cleaned, fn($l) => $l !== ''));
            }
            return [$desc];
        }

        return [];
    }
}
