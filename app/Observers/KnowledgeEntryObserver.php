<?php

namespace App\Observers;

use App\Models\KnowledgeEntry;

class KnowledgeEntryObserver
{
    public function creating(KnowledgeEntry $knowledgeEntry): void
    {
        //
    }

    public function saving(KnowledgeEntry $knowledgeEntry): void
    {
        //
    }
}
