<?php

use App\Models\KnowledgeSuggestion;
use App\Models\KnowledgeEntry;
use App\Models\User;

it('rejects all pending suggestions in bulk', function () {
    $admin = User::factory()->create([
        'role' => 'Admin',
    ]);

    $suggestions = KnowledgeSuggestion::factory()->count(3)->create();

    $response = $this->actingAs($admin)->post(route('knowledge.suggestions.rejectAll'));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $suggestions->each(function (KnowledgeSuggestion $suggestion): void {
        $this->assertDatabaseMissing('knowledge_suggestions', [
            'id' => $suggestion->id,
        ]);
    });
});

it('approves a suggestion with edited data', function () {
    $admin = User::factory()->create([
        'role' => 'Admin',
    ]);

    $suggestion = KnowledgeSuggestion::factory()->create([
        'title' => 'Old title',
        'answer' => 'Old answer',
        'source' => 'manual',
        'approved_at' => null,
    ]);

    $response = $this->actingAs($admin)->post(route('knowledge.suggestions.approve', $suggestion), [
        'title' => 'New title',
        'answer' => 'New answer',
        'source' => 'api',
    ]);

    $response
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('knowledge_entries', [
        'title' => 'New title',
        'answer' => 'New answer',
        'source' => 'api',
    ]);

    $this->assertDatabaseMissing('knowledge_suggestions', [
        'id' => $suggestion->id,
    ]);
});
