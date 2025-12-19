<?php

use App\Models\KnowledgeEntry;
use App\Models\KnowledgeSuggestion;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

it('shows superadmin dashboard data and admin management actions', function () {
    $superadmin = User::factory()->create([
        'role' => 'Superadmin',
    ]);

    User::factory()->create([
        'role' => 'Admin',
    ]);

    KnowledgeEntry::create([
        'title' => 'Published Entry',
        'answer' => 'Answer',
        'source' => 'manual',
        'is_published' => true,
    ]);

    KnowledgeEntry::create([
        'title' => 'Draft Entry',
        'answer' => 'Draft',
        'source' => 'manual',
        'is_published' => false,
    ]);

    KnowledgeSuggestion::create([
        'title' => 'Pending Suggestion',
        'answer' => 'Suggest',
        'source' => 'api',
        'approved_at' => null,
    ]);

    DB::table('sessions')->insert([
        'id' => (string) Str::uuid(),
        'user_id' => $superadmin->id,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Pest',
        'payload' => '[]',
        'last_activity' => Carbon::now()->timestamp,
    ]);

    $response = $this->actingAs($superadmin)->get('/');

    $response
        ->assertOk()
        ->assertSee('Kelola Admin')
        ->assertSee('Superadmin')
        ->assertSee('Knowledge Publish')
        ->assertDontSee('Review Suggestion');
});

it('shows admin dashboard without admin management actions', function () {
    $admin = User::factory()->create([
        'role' => 'Admin',
    ]);

    KnowledgeEntry::create([
        'title' => 'Published Entry',
        'answer' => 'Answer',
        'source' => 'manual',
        'is_published' => true,
    ]);

    KnowledgeEntry::create([
        'title' => 'Draft Entry',
        'answer' => 'Draft',
        'source' => 'manual',
        'is_published' => false,
    ]);

    KnowledgeSuggestion::create([
        'title' => 'Pending Suggestion',
        'answer' => 'Suggest',
        'source' => 'api',
        'approved_at' => null,
    ]);

    $response = $this->actingAs($admin)->get('/');

    $response
        ->assertOk()
        ->assertSee('Review Suggestion')
        ->assertSee('Draft Knowledge')
        ->assertSee('History Chat')
        ->assertDontSee('Kelola Admin');
});
