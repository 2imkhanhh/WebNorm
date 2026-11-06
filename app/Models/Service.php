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
}

