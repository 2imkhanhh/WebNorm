<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProjectCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'sort_order',
    ];

    protected $casts = [
        'name' => 'json',
        'sort_order' => 'integer',
    ];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_category_project', 'project_category_id', 'project_id');
    }

    public function getName(string $lang = 'vi'): string
    {
        if (is_array($this->name)) {
            return $this->name[$lang] ?? $this->name['vi'] ?? '';
        }
        return (string) ($this->name ?? '');
    }
}

