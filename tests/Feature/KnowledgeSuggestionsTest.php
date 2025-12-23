<?php

use App\Models\KnowledgeEntry;
use App\Models\KnowledgeSuggestion;
use App\Models\User;

it('rejects all pending suggestions in bulk', function () {
    $admin = User::factory()->create([
        'role' => 'Admin',
    ]);

    $suggestions = KnowledgeSuggestion::factory()->count(3)->create();
    $processed = KnowledgeSuggestion::factory()->create([
        'approved_at' => now(),
        'is_published' => false,
    ]);

    $response = $this->actingAs($admin)->post(route('knowledge.suggestions.rejectAll'));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $suggestions->each(function (KnowledgeSuggestion $suggestion): void {
        $this->assertDatabaseMissing('knowledge_suggestions', [
            'id' => $suggestion->id,
        ]);
    });

    $this->assertDatabaseHas('knowledge_suggestions', [
        'id' => $processed->id,
    ]);
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

    $entry = KnowledgeEntry::query()->where('title', 'New title')->first();

    expect($entry)->not->toBeNull();
    expect($entry->answer)->toBe('New answer');
    expect($entry->source)->toBe('api');
    expect($entry->is_published)->toBeTrue();
    expect($entry->created_by)->toBe($admin->id);
    expect($entry->approved_by)->toBe($admin->id);
    expect($entry->approved_at)->not->toBeNull();

    $this->assertDatabaseMissing('knowledge_suggestions', [
        'id' => $suggestion->id,
    ]);
});

it('rejects a suggestion and marks it processed', function () {
    $admin = User::factory()->create([
        'role' => 'Admin',
    ]);

    $suggestion = KnowledgeSuggestion::factory()->create([
        'is_published' => true,
        'approved_at' => null,
    ]);

    $response = $this->actingAs($admin)->post(route('knowledge.suggestions.reject', $suggestion));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('knowledge_suggestions', [
        'id' => $suggestion->id,
        'is_published' => false,
        'approved_by' => $admin->id,
    ]);

    $this->assertNotNull($suggestion->fresh()->approved_at);
});
