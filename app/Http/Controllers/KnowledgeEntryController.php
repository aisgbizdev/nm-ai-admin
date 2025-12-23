<?php

namespace App\Http\Controllers;

use App\Http\Requests\KnowledgeEntryStoreRequest;
use App\Http\Requests\KnowledgeEntryUpdateRequest;
use App\Http\Requests\KnowledgeSuggestionApproveRequest;
use App\Models\KnowledgeEntry;
use App\Models\KnowledgeSuggestion;
use App\Services\KnowledgeEntryService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class KnowledgeEntryController extends Controller
{
    public function __construct(private KnowledgeEntryService $knowledgeEntryService) {}

    private function ensureInternalApprover(): void
    {
        abort_unless(auth()->check(), 401);

        $role = auth()->user()->role ?? null;
        abort_unless(in_array($role, ['Superadmin', 'Admin'], true), 403);
    }

    public function index(Request $request): View
    {
        $this->ensureInternalApprover();

        $q = trim((string) $request->get('q', ''));
        $published = $request->get('published'); // 1|0|null

        [
            'entries' => $entries,
            'collections' => $collections,
            'recent' => $recent,
            'count' => $count,
            'countDraft' => $countDraft
        ] = $this->knowledgeEntryService->getIndexData($q, $published);

        return view('knowledge.index', compact(
            'entries', 'q', 'published',
            'collections', 'recent', 'count', 'countDraft'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->ensureInternalApprover();

        return view('knowledge.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KnowledgeEntryStoreRequest $request): RedirectResponse
    {
        $this->ensureInternalApprover();

        $this->knowledgeEntryService->createEntry(
            $request->validated(),
            (int) auth()->id()
        );

        return redirect()
            ->route('knowledge.index')
            ->with('success', 'Knowledge berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KnowledgeEntry $entry): View
    {
        $this->ensureInternalApprover();

        return view('knowledge.edit', compact('entry'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(KnowledgeEntryUpdateRequest $request, KnowledgeEntry $entry): RedirectResponse
    {
        $this->ensureInternalApprover();

        $this->knowledgeEntryService->updateEntry(
            $entry,
            $request->validated(),
            (int) auth()->id()
        );

        return redirect()
            ->route('knowledge.index')
            ->with('success', 'Knowledge berhasil diupdate.');
    }

    public function approve(KnowledgeEntry $entry): RedirectResponse
    {
        $this->ensureInternalApprover();

        $this->knowledgeEntryService->approveEntry($entry, (int) auth()->id());

        return back()->with('success', 'Entry berhasil di-approve & dipublish.');
    }

    public function toggleActive(KnowledgeEntry $entry): RedirectResponse
    {
        $this->ensureInternalApprover();

        $this->knowledgeEntryService->toggleActive($entry, (int) auth()->id());

        return back()->with('success', 'Status aktif berhasil diubah.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KnowledgeEntry $entry): RedirectResponse
    {
        $this->ensureInternalApprover();

        $this->knowledgeEntryService->deleteEntry($entry);

        return back()->with('success', 'Entry berhasil dihapus.');
    }

    public function drafts(Request $request): View
    {
        $this->ensureInternalApprover();

        $q = trim((string) $request->get('q', ''));
        $drafts = $this->knowledgeEntryService->getDraftEntries($q);

        return view('knowledge.drafts', compact('drafts', 'q'));
    }

    // ==================================
    // SUGGESTIONS (knowledge_suggestions)
    // ==================================

    /**
     * List pending suggestions (belum diproses)
     */
    public function suggestions(Request $request): View
    {
        $this->ensureInternalApprover();

        $q = trim((string) $request->get('q', ''));

        $suggestions = KnowledgeSuggestion::query()
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
    public function approveSuggestion(KnowledgeSuggestionApproveRequest $request, KnowledgeSuggestion $suggestion): RedirectResponse
    {
        $this->ensureInternalApprover();

        $entry = $this->knowledgeEntryService->approveSuggestion(
            $suggestion,
            $request->validated(),
            (int) auth()->id()
        );

        if ($entry === null) {
            return back()->with('success', 'Suggestion sudah diproses sebelumnya.');
        }

        return back()->with(
            'success',
            'Suggestion berhasil di-approve dan dipublish ke Knowledge Entries.'
        );
    }

    /**
     * Reject suggestion (tandai diproses tapi tetap tidak publish)
     */
    public function rejectSuggestion(KnowledgeSuggestion $suggestion): RedirectResponse
    {
        $this->ensureInternalApprover();

        $rejected = $this->knowledgeEntryService->rejectSuggestion($suggestion, (int) auth()->id());

        if (! $rejected) {
            return back()->with('success', 'Suggestion sudah diproses sebelumnya.');
        }

        return back()->with('success', 'Suggestion berhasil ditolak.');
    }

    /**
     * Reject all pending suggestions at once.
     */
    public function rejectAllSuggestions(): RedirectResponse
    {
        $this->ensureInternalApprover();

        $pendingCount = $this->knowledgeEntryService->rejectAllPendingSuggestions();

        if ($pendingCount === 0) {
            return back()->with('success', 'Tidak ada suggestion pending.');
        }

        return back()->with('success', "Semua suggestion pending ({$pendingCount}) berhasil dihapus.");
    }
}
