<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeEntry;
use App\Models\KnowledgeSuggestion;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Dashboard data source.
     */
    public function index(): View
    {
        $user = Auth::user();
        $isSuperadmin = $user && $user->role === 'Superadmin';

        $publishedEntries = KnowledgeEntry::where('is_published', true)->count();
        $draftEntries = KnowledgeEntry::where('is_published', false)->count();
        $pendingSuggestions = KnowledgeSuggestion::whereNull('approved_at')->count();

        $stats = $isSuperadmin
            ? $this->superadminStats($publishedEntries, $pendingSuggestions)
            : $this->adminStats($publishedEntries, $draftEntries, $pendingSuggestions);

        $activities = $isSuperadmin
            ? $this->superadminActivities()
            : $this->adminActivities();

        return view('dashboard', compact('user', 'stats', 'activities', 'isSuperadmin'));
    }

    private function superadminStats(int $publishedEntries, int $pendingSuggestions): array
    {
        $totalSuperadmin = User::where('role', 'Superadmin')->count();
        $totalAdmin = User::where('role', 'Admin')->count();

        return [
            [
                'title' => 'Superadmin',
                'value' => $totalSuperadmin,
                'trend' => 'Akses penuh',
                'icon' => 'fa-solid fa-user-shield',
                'color' => 'bg-indigo-50 text-indigo-700',
            ],
            [
                'title' => 'Admin',
                'value' => $totalAdmin,
                'trend' => 'Manajemen aktif',
                'icon' => 'fa-solid fa-user-gear',
                'color' => 'bg-blue-50 text-blue-700',
            ],
            [
                'title' => 'Knowledge Publish',
                'value' => $publishedEntries,
                'trend' => 'Siap dipakai',
                'icon' => 'fa-solid fa-circle-check',
                'color' => 'bg-emerald-50 text-emerald-700',
            ],
            [
                'title' => 'Suggestion Pending',
                'value' => $pendingSuggestions,
                'trend' => $pendingSuggestions > 0 ? 'Butuh review' : 'Bersih',
                'icon' => 'fa-solid fa-inbox',
                'color' => 'bg-amber-50 text-amber-700',
            ],
        ];
    }

    private function adminStats(int $publishedEntries, int $draftEntries, int $pendingSuggestions): array
    {
        $historySessions = DB::table('sessions')
            ->whereNotNull('user_id')
            ->count();

        return [
            [
                'title' => 'Knowledge Publish',
                'value' => $publishedEntries,
                'trend' => 'Terbit',
                'icon' => 'fa-solid fa-circle-check',
                'color' => 'bg-emerald-50 text-emerald-700',
            ],
            [
                'title' => 'Draft Knowledge',
                'value' => $draftEntries,
                'trend' => 'Perlu review',
                'icon' => 'fa-solid fa-pen-to-square',
                'color' => 'bg-blue-50 text-blue-700',
            ],
            [
                'title' => 'Suggestion Pending',
                'value' => $pendingSuggestions,
                'trend' => $pendingSuggestions > 0 ? 'Butuh approval' : 'Tidak ada antrian',
                'icon' => 'fa-solid fa-inbox',
                'color' => 'bg-amber-50 text-amber-700',
            ],
            [
                'title' => 'History Chat',
                'value' => $historySessions,
                'trend' => 'Log tersedia',
                'icon' => 'fa-solid fa-comments',
                'color' => 'bg-indigo-50 text-indigo-700',
            ],
        ];
    }

    private function superadminActivities(): Collection
    {
        return DB::table('sessions')
            ->whereNotNull('user_id')
            ->orderByDesc('last_activity')
            ->limit(5)
            ->get()
            ->map(function ($session) {
                $user = User::find($session->user_id);

                return [
                    'title' => $user?->name ?? 'User',
                    'time' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                    'status' => $user?->role ?? 'Session',
                ];
            });
    }

    private function adminActivities(): Collection
    {
        return KnowledgeEntry::query()
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get()
            ->map(function (KnowledgeEntry $entry) {
                return [
                    'title' => $entry->title,
                    'time' => optional($entry->updated_at)->diffForHumans() ?? '-',
                    'status' => $entry->is_published ? 'Published' : 'Draft',
                ];
            });
    }
}
