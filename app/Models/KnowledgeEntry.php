<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeEntry extends Model
{
    protected $fillable = [
        'title',
        'answer',
        'source',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];
}
