<?php

namespace App\Models;

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

    public function getTitle(string $lang = 'vi'): string
    {
        return is_array($this->title) ? ($this->title[$lang] ?? $this->title['vi'] ?? '') : ($this->title ?? '');
    }

    public function getDescription(string $lang = 'vi'): string
    {
        return is_array($this->description) ? ($this->description[$lang] ?? $this->description['vi'] ?? '') : ($this->description ?? '');
    }

    /**
     * Get bullet list items for frontend service-content-wrapper
     */
    public function getListItems(string $lang = 'vi'): array
    {
        // 1. If explicit 'content' array exists for language
        if (is_array($this->content)) {
            if (isset($this->content[$lang]) && is_array($this->content[$lang]) && !empty($this->content[$lang])) {
                return $this->content[$lang];
            }
            if (isset($this->content['vi']) && is_array($this->content['vi']) && !empty($this->content['vi'])) {
                return $this->content['vi'];
            }
            // Flat array of strings: ["Item 1", "Item 2"]
            if (!empty($this->content) && isset($this->content[0])) {
                return $this->content;
            }
        }

        // 2. Parse from description
        $desc = $this->getDescription($lang);
        if (!empty($desc)) {
            // Newline-separated lines
            if (str_contains($desc, "\n")) {
                $lines = array_map('trim', explode("\n", $desc));
                return array_values(array_filter($lines, fn($l) => $l !== ''));
            }
            // Comma-separated list
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

