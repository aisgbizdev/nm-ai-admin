<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\KnowledgeApiStoreRequest;
use App\Services\KnowledgeApiService;
use Illuminate\Http\JsonResponse;

class KnowledgeApiController extends Controller
{
    public function __construct(private KnowledgeApiService $knowledgeApiService) {}

    /**
     * GET /api/v1/knowledge
     * Ambil knowledge yang sudah publish dari knowledge_entries
     */
    public function index(): JsonResponse
    {
        $knowledge = $this->knowledgeApiService->getPublishedKnowledge();

        return response()->json([
            'status' => true,
            'knowledge' => $knowledge,
        ]);
    }

    /**
     * POST /api/v1/knowledge
     * Submit knowledge baru -> masuk ke knowledge_suggestions dulu (pending review)
     */
    public function store(KnowledgeApiStoreRequest $request): JsonResponse
    {
        $suggestion = $this->knowledgeApiService->storeSuggestion($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Suggestion submitted (pending review)',
            'data' => $suggestion,
        ], 201);
    }
}
