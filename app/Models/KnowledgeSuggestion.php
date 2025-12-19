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

    // ✅ Default kalau gak dikirim
    protected $attributes = [
        'source' => 'ai_generated',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $m) {
            if (empty($m->source)) {
                $m->source = 'ai_generated';
            } else {
                // rapihin biar konsisten
                $m->source = strtolower(trim($m->source));
            }
        });
    }
}
