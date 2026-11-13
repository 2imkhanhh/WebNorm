<?php

namespace App\Models;

use App\Helpers\NormHelper;
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

    public function getRole(?string $lang = null): string
    {
        return NormHelper::val($this->role, '', $lang);
    }

    public function getContent(?string $lang = null): string
    {
        return NormHelper::val($this->content, '', $lang);
    }
}

