<?php

namespace App\Services;

use App\Models\KnowledgeEntry;
use App\Models\KnowledgeSuggestion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class KnowledgeEntryService
{
    public function getIndexData(string $q, ?string $published): array
    {
        $entries = KnowledgeEntry::query()
            ->where('is_published', true)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('title', 'like', "%{$q}%")
                        ->orWhere('answer', 'like', "%{$q}%")
                        ->orWhere('source', 'like', "%{$q}%");
                });
            })
            ->when($published !== null && $published !== '', fn ($query) => $query->where('is_published', (bool) $published))
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();

        $palette = [
            'bg-blue-50 text-blue-700',
            'bg-indigo-50 text-indigo-700',
            'bg-emerald-50 text-emerald-700',
            'bg-amber-50 text-amber-700',
            'bg-rose-50 text-rose-700',
            'bg-violet-50 text-violet-700',
        ];

        $collections = KnowledgeEntry::query()
            ->select('source', DB::raw('count(*) as total'), DB::raw('max(updated_at) as last_update'))
            ->groupBy('source')
            ->orderByDesc('total')
            ->get()
            ->map(function ($row) use ($palette) {
                $source = $row->source ?: 'manual';
                $idx = abs(crc32((string) $source)) % count($palette);

                return [
                    'title' => ucfirst($source),
                    'desc' => 'Dokumen dengan sumber '.$source,
                    'count' => $row->total,
                    'updated' => $row->last_update ? Carbon::parse($row->last_update)->diffForHumans() : '-',
                    'tags' => [],
                    'color' => $palette[$idx],
                ];
            })
            ->values()
            ->all();

        $recent = KnowledgeEntry::query()
            ->where('is_published', true)
            ->orderByDesc('updated_at')
            ->limit(4)
            ->get(['title', 'source', 'updated_at'])
            ->map(function ($e) {
                return [
                    'title' => $e->title,
                    'owner' => ucfirst($e->source ?? 'manual'),
                    'time' => $e->updated_at ? $e->updated_at->diffForHumans() : '-',
                ];
            })
            ->values()
            ->all();

        $count = KnowledgeSuggestion::count();

        $countDraft = KnowledgeEntry::where('is_published', false)->count();

        return compact('entries', 'collections', 'recent', 'count', 'countDraft');
    }

    public function getDraftEntries(string $q): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return KnowledgeEntry::query()
            ->where('is_published', false)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('title', 'like', "%{$q}%")
                        ->orWhere('answer', 'like', "%{$q}%")
                        ->orWhere('source', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();
    }

    public function createEntry(array $data, int $userId): KnowledgeEntry
    {
        return DB::transaction(function () use ($data): KnowledgeEntry {
            $entry = new KnowledgeEntry;
            $entry->title = $data['title'];
            $entry->answer = $data['answer'];
            $entry->source = $data['source'];
            $entry->is_published = $data['is_published'] ?? true;
            $entry->save();

            return $entry;
        });
    }

    public function updateEntry(KnowledgeEntry $entry, array $data, int $userId): KnowledgeEntry
    {
        return DB::transaction(function () use ($entry, $data): KnowledgeEntry {
            $entry->title = $data['title'];
            $entry->answer = $data['answer'];
            $entry->source = $data['source'];
            $entry->is_published = $data['is_published'] ?? true;

            $entry->save();

            return $entry;
        });
    }

    public function approveEntry(KnowledgeEntry $entry, int $userId): KnowledgeEntry
    {
        return DB::transaction(function () use ($entry): KnowledgeEntry {
            $entry->is_published = true;
            $entry->save();

            return $entry;
        });
    }

    public function toggleActive(KnowledgeEntry $entry, int $userId): KnowledgeEntry
    {
        return DB::transaction(function () use ($entry): KnowledgeEntry {
            $entry->is_published = ! $entry->is_published;
            $entry->save();

            return $entry;
        });
    }

    public function deleteEntry(KnowledgeEntry $entry): void
    {
        DB::transaction(static function () use ($entry): void {
            $entry->delete();
        });
    }

    public function approveSuggestion(KnowledgeSuggestion $suggestion, array $data, int $userId): ?KnowledgeEntry
    {
        return DB::transaction(function () use ($suggestion, $data): ?KnowledgeEntry {
            if ($suggestion->approved_at) {
                return null;
            }

            $entry = KnowledgeEntry::query()->create([
                'title' => $data['title'],
                'answer' => $data['answer'],
                'source' => $data['source'] ?? 'manual',
                'is_published' => true,
            ]);

            $suggestion->delete();

            return $entry;
        });
    }

    public function rejectSuggestion(KnowledgeSuggestion $suggestion, int $userId): bool
    {
        return DB::transaction(function () use ($suggestion): bool {
            if ($suggestion->approved_at) {
                return false;
            }

            $suggestion->is_published = false;
            $suggestion->save();

            return true;
        });
    }

    public function rejectAllPendingSuggestions(): int
    {
        return DB::transaction(static function (): int {
            $pendingQuery = KnowledgeSuggestion::query()->whereNull('approved_at');
            $pendingCount = $pendingQuery->count();

            if ($pendingCount === 0) {
                return 0;
            }

            $pendingQuery->delete();

            return $pendingCount;
        });
    }
}
