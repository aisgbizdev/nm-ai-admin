<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeSuggestion extends Model
{
    use HasFactory;

    protected $table = 'knowledge_suggestions';

    protected $fillable = [
        'title',
        'answer',
        'source',
    ];

    protected $attributes = [
        'source' => 'Manual',
    ];
}
