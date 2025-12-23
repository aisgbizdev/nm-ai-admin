<?php

namespace App\Services;

use App\Models\KnowledgeEntry;
use App\Models\KnowledgeSuggestion;
use Illuminate\Support\Collection;

class KnowledgeApiService
{
    public function getPublishedKnowledge(): Collection
    {
        return KnowledgeEntry::query()
            ->where('is_published', true)
            ->orderByDesc('updated_at')
            ->get();
    }

    public function storeSuggestion(array $data): KnowledgeSuggestion
    {
        return KnowledgeSuggestion::query()->create([
            'title' => $data['title'],
            'answer' => $data['answer'],
            'source' => $data['source'] ?? null,
        ]);
    }
}
