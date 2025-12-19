<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeEntry;
use App\Models\KnowledgeSuggestion;
use Illuminate\Http\Request;

class KnowledgeApiController extends Controller
{
    /**
     * GET /api/v1/knowledge
     * Ambil knowledge yang sudah publish dari knowledge_entries
     */
    public function index()
    {
        $knowledge = KnowledgeEntry::query()
            ->where('is_published', 1)
            ->orderByDesc('updated_at')
            ->get();

        return response()->json([
            'status' => true,
            'knowledge' => $knowledge,
        ]);
    }

    /**
     * POST /api/v1/knowledge
     * Submit knowledge baru -> masuk ke knowledge_suggestions dulu (pending review)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'], // markdown boleh
            'source' => ['nullable', 'string'],
        ]);

        $suggestion = KnowledgeSuggestion::create([
            'title' => $validated['title'],
            'answer' => $validated['answer'],
            'source' => $validated['source'],

            // pending (belum publish)
            'is_published' => 0,

            // optional kalau pakai auth (kalau gak ada auth, bakal null dan aman)
            'created_by' => auth()->check() ? auth()->id() : null,

            // pending approval
            'approved_by' => null,
            'approved_at' => null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Suggestion submitted (pending review)',
            'data' => $suggestion,
        ], 201);
    }
}
