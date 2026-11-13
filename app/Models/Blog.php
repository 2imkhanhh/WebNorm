<?php

namespace App\Models;

use App\Helpers\NormHelper;
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

    public function getTitle(?string $lang = null): string
    {
        return NormHelper::val($this->title, '', $lang);
    }

    public function getExcerpt(?string $lang = null): string
    {
        return NormHelper::val($this->excerpt, '', $lang);
    }

    public function getContent(?string $lang = null): string
    {
        return NormHelper::val($this->content, '', $lang);
    }

    public function getAuthorRole(?string $lang = null): string
    {
        return NormHelper::val($this->author_role, '', $lang);
    }
}

