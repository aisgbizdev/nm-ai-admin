<?php

use App\Http\Controllers\Admin\FirestoreHistoryController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KnowledgeEntryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('dashboard');

    Route::prefix('knowledge')->group(function () {
        Route::get('/', [KnowledgeEntryController::class, 'index'])->name('knowledge.index');
        Route::get('/create', [KnowledgeEntryController::class, 'create'])->name('knowledge.create');
        Route::post('/', [KnowledgeEntryController::class, 'store'])->name('knowledge.store');

        Route::get('/drafts', [KnowledgeEntryController::class, 'drafts'])->name('knowledge.drafts');

        Route::get('/{entry}/edit', [KnowledgeEntryController::class, 'edit'])->name('knowledge.edit');
        Route::put('/{entry}', [KnowledgeEntryController::class, 'update'])->name('knowledge.update');

        // “hapus” versi aman: nonaktifkan
        Route::delete('/{entry}', [KnowledgeEntryController::class, 'destroy'])->name('knowledge.destroy');

        Route::patch('/{entry}/approve', [KnowledgeEntryController::class, 'approve'])->name('knowledge.approve');
        Route::patch('/{entry}/toggle-active', [KnowledgeEntryController::class, 'toggleActive'])->name('knowledge.toggleActive');

        // ✅ Suggestions (pending dari API)
        Route::get('/suggestions', [KnowledgeEntryController::class, 'suggestions'])
            ->name('knowledge.suggestions');

        Route::post('/suggestions/reject-all', [KnowledgeEntryController::class, 'rejectAllSuggestions'])
            ->name('knowledge.suggestions.rejectAll');

        Route::post('/suggestions/{suggestion}/approve', [KnowledgeEntryController::class, 'approveSuggestion'])
            ->name('knowledge.suggestions.approve');

        Route::post('/suggestions/{suggestion}/reject', [KnowledgeEntryController::class, 'rejectSuggestion'])
            ->name('knowledge.suggestions.reject');
    });

    Route::middleware(['role:Superadmin'])->controller(UserController::class)->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{admin}/edit', 'edit')->name('edit');
        Route::put('/{admin}', 'update')->name('update');
        Route::get('/export', 'export')->name('export');
        Route::delete('/{admin}', 'destroy')->name('destroy');
    });

    Route::get('/admin/history/data', [FirestoreHistoryController::class, 'index'])->name('admin.history.data');

    Route::controller(HistoryController::class)->group(function () {
        Route::get('/history', 'index')->name('history.index');
        Route::get('/history/{sessionId}', 'show')->name('history.show');
    });

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

require __DIR__.'/auth.php';
