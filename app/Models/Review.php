<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'author',
        'role',
        'avatar',
        'content',
        'rating',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'role' => 'json',
        'content' => 'json',
        'rating' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function getRole(string $lang = 'vi'): string
    {
        return is_array($this->role) ? ($this->role[$lang] ?? $this->role['vi'] ?? '') : ($this->role ?? '');
    }

    public function getContent(string $lang = 'vi'): string
    {
        return is_array($this->content) ? ($this->content[$lang] ?? $this->content['vi'] ?? '') : ($this->content ?? '');
    }
}

