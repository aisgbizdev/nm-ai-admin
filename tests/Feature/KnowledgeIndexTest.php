<?php

use App\Models\KnowledgeSuggestion;
use App\Models\User;

it('shows pending suggestions badge on knowledge index', function () {
    $admin = User::factory()->create([
        'role' => 'Admin',
    ]);

    KnowledgeSuggestion::factory()->count(2)->create([
        'approved_at' => null,
    ]);

    KnowledgeSuggestion::factory()->create([
        'approved_at' => now(),
    ]);

    $response = $this->actingAs($admin)->get(route('knowledge.index'));

    $response
        ->assertOk()
        ->assertSee('Suggestions', false)
        ->assertSee(
            '<span class="inline-flex items-center justify-center rounded-full bg-rose-500 px-2 py-0.5 text-[11px] font-bold uppercase tracking-wide text-white min-w-[1.75rem]">2</span>',
            false
        );
});
