<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudioDraft extends Model
{
    protected $fillable = [
        'module',
        'payload',
    ];

    protected $casts = [
        'payload' => 'json',
    ];
}

