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
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'approved_at' => 'datetime',
    ];
}
