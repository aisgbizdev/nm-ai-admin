<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeEntry;
use App\Models\KnowledgeSuggestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KnowledgeEntryController extends Controller
{
    private function ensureInternalApprover(): void
    {
        abort_unless(auth()->check(), 401);

        $role = auth()->user()->role ?? null;
        abort_unless(in_array($role, ['Superadmin', 'Admin'], true), 403);
    }

    // =========================
    // ENTRIES (knowledge_entries)
    // =========================
    public function index(Request $request)
    {
        $this->ensureInternalApprover();

        $q = trim((string) $request->get('q', ''));
        $published = $request->get('published'); // 1|0|null

        $entries = KnowledgeEntry::query()
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
                    'updated' => $row->last_update ? \Carbon\Carbon::parse($row->last_update)->diffForHumans() : '-',
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

        return view('knowledge.index', compact(
            'entries', 'q', 'published',
            'collections', 'recent'
        ));
    }

    public function create()
    {
        $this->ensureInternalApprover();

        return view('knowledge.create');
    }

    public function store(Request $request)
    {
        $this->ensureInternalApprover();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'answer' => ['required', 'string'],
            'source' => ['required', 'string', 'max:255'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        return DB::transaction(function () use ($data, $request) {
            $entry = new KnowledgeEntry;
            $entry->title = $data['title'];
            $entry->answer = $data['answer'];
            $entry->source = $data['source'];
            $entry->is_published = $request->boolean('is_published', true);

            $entry->save();

            return redirect()
                ->route('knowledge.index')
                ->with('success', 'Knowledge berhasil dibuat.');
        });
    }

    public function edit(KnowledgeEntry $entry)
    {
        $this->ensureInternalApprover();

        return view('knowledge.edit', compact('entry'));
    }

    public function update(Request $request, KnowledgeEntry $entry)
    {
        $this->ensureInternalApprover();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'answer' => ['required', 'string'],
            'source' => ['required', 'string', 'max:255'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        return DB::transaction(function () use ($data, $entry, $request) {
            $entry->title = $data['title'];
            $entry->answer = $data['answer'];
            $entry->source = $data['source'];
            $entry->is_published = $request->boolean('is_published', true);

            $entry->save();

            return redirect()
                ->route('knowledge.index')
                ->with('success', 'Knowledge berhasil diupdate.');
        });
    }

    public function approve(KnowledgeEntry $entry)
    {
        $this->ensureInternalApprover();

        return DB::transaction(function () use ($entry) {
            $entry->is_published = true;
            $entry->save();

            return back()->with('success', 'Entry berhasil di-approve & dipublish.');
        });
    }

    public function toggleActive(KnowledgeEntry $entry)
    {
        $this->ensureInternalApprover();

        $entry->is_published = ! $entry->is_published;
        $entry->save();

        return back()->with('success', 'Status aktif berhasil diubah.');
    }

    public function destroy(KnowledgeEntry $entry)
    {
        $this->ensureInternalApprover();

        $entry->delete();

        return back()->with('success', 'Entry berhasil dihapus.');
    }

    // ==================================
    // SUGGESTIONS (knowledge_suggestions)
    // ==================================

    /**
     * List pending suggestions (belum diproses)
     */
    public function suggestions(Request $request)
    {
        $this->ensureInternalApprover();

        $q = trim((string) $request->get('q', ''));

        $suggestions = KnowledgeSuggestion::query() // pending = belum diproses
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('title', 'like', "%{$q}%")
                        ->orWhere('answer', 'like', "%{$q}%")
                        ->orWhere('source', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('knowledge.suggestions', compact('suggestions', 'q'));
    }

    /**
     * Approve suggestion -> copy ke knowledge_entries, lalu HAPUS dari knowledge_suggestions
     */
    public function approveSuggestion(Request $request, KnowledgeSuggestion $suggestion)
    {
        $this->ensureInternalApprover();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'answer' => ['required', 'string'],
            'source' => ['required', 'string', 'max:255'],
        ]);

        return DB::transaction(function () use ($suggestion, $data) {
            if ($suggestion->approved_at) {
                return back()->with('success', 'Suggestion sudah diproses sebelumnya.');
            }

            KnowledgeEntry::query()->create([
                'title' => $data['title'],
                'answer' => $data['answer'],
                'source' => $data['source'] ?? 'manual',
                'is_published' => true,
            ]);

            $suggestion->delete();

            return back()->with(
                'success',
                'Suggestion berhasil di-approve dan dipublish ke Knowledge Entries.'
            );
        });
    }

    /**
     * Reject suggestion (tandai diproses tapi tetap tidak publish)
     */
    public function rejectSuggestion(KnowledgeSuggestion $suggestion)
    {
        $this->ensureInternalApprover();

        return DB::transaction(function () use ($suggestion) {
            if ($suggestion->approved_at) {
                return back()->with('success', 'Suggestion sudah diproses sebelumnya.');
            }

            $suggestion->is_published = false;
            $suggestion->approved_by = auth()->id();
            $suggestion->approved_at = now();
            $suggestion->save();

            return back()->with('success', 'Suggestion berhasil ditolak.');
        });
    }

    /**
     * Reject all pending suggestions at once.
     */
    public function rejectAllSuggestions(): RedirectResponse
    {
        $this->ensureInternalApprover();

        $pendingCount = KnowledgeSuggestion::whereNull('approved_at')->count();

        if ($pendingCount === 0) {
            return back()->with('success', 'Tidak ada suggestion pending.');
        }

        return DB::transaction(function () use ($pendingCount): RedirectResponse {
            KnowledgeSuggestion::query()
                ->whereNull('approved_at')
                ->delete();

            return back()->with('success', "Semua suggestion pending ({$pendingCount}) berhasil dihapus.");
        });
    }
}
