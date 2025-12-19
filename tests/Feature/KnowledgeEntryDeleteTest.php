<?php

use App\Models\KnowledgeEntry;
use App\Models\User;

it('deletes a knowledge entry permanently', function () {
    $admin = User::factory()->create([
        'role' => 'Admin',
    ]);

    $entry = KnowledgeEntry::create([
        'title' => 'Sample Entry',
        'answer' => 'Sample answer',
        'source' => 'manual',
        'is_published' => true,
    ]);

    $response = $this->actingAs($admin)->delete(route('knowledge.destroy', $entry->id));

    $response->assertRedirect();
    $response->assertSessionHas('success');
    $this->assertModelMissing($entry);
});
