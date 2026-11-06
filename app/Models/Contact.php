<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'services',
        'budget',
        'message',
        'status',
        'notes',
    ];

    protected $casts = [
        'services' => 'json',
    ];
}

