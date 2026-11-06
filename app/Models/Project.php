<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'thumbnail',
        'gallery',
        'client',
        'year',
        'description',
        'content',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'title' => 'json',
        'category' => 'json',
        'gallery' => 'json',
        'description' => 'json',
        'content' => 'json',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getTitle(string $lang = 'vi'): string
    {
        return is_array($this->title) ? ($this->title[$lang] ?? $this->title['vi'] ?? '') : ($this->title ?? '');
    }

    public function getCategory(string $lang = 'vi'): string
    {
        return is_array($this->category) ? ($this->category[$lang] ?? $this->category['vi'] ?? '') : ($this->category ?? '');
    }

    public function getDescription(string $lang = 'vi'): string
    {
        return is_array($this->description) ? ($this->description[$lang] ?? $this->description['vi'] ?? '') : ($this->description ?? '');
    }
}

