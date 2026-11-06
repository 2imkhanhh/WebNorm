<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'thumbnail',
        'excerpt',
        'content',
        'author',
        'published_at',
        'status',
    ];

    protected $casts = [
        'title' => 'json',
        'category' => 'json',
        'excerpt' => 'json',
        'content' => 'json',
        'published_at' => 'datetime',
    ];

    public function getTitle(string $lang = 'vi'): string
    {
        return is_array($this->title) ? ($this->title[$lang] ?? $this->title['vi'] ?? '') : ($this->title ?? '');
    }

    public function getExcerpt(string $lang = 'vi'): string
    {
        return is_array($this->excerpt) ? ($this->excerpt[$lang] ?? $this->excerpt['vi'] ?? '') : ($this->excerpt ?? '');
    }
}

