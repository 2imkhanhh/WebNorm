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
        'author_role',
        'author_avatar',
        'published_at',
        'status',
        'is_featured',
    ];

    protected $casts = [
        'title' => 'json',
        'category' => 'json',
        'excerpt' => 'json',
        'content' => 'json',
        'author_role' => 'json',
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
    ];

    public function getTitle(string $lang = 'vi'): string
    {
        return is_array($this->title) ? ($this->title[$lang] ?? $this->title['vi'] ?? '') : ($this->title ?? '');
    }

    public function getExcerpt(string $lang = 'vi'): string
    {
        return is_array($this->excerpt) ? ($this->excerpt[$lang] ?? $this->excerpt['vi'] ?? '') : ($this->excerpt ?? '');
    }

    public function getContent(string $lang = 'vi'): string
    {
        return is_array($this->content) ? ($this->content[$lang] ?? $this->content['vi'] ?? '') : ($this->content ?? '');
    }

    public function getAuthorRole(string $lang = 'vi'): string
    {
        return is_array($this->author_role) ? ($this->author_role[$lang] ?? $this->author_role['vi'] ?? '') : ($this->author_role ?? '');
    }
}

