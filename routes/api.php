<?php

use App\Http\Controllers\Api\KnowledgeApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('bearer')->group(function () {
    Route::prefix('knowledge')->group(function () {
        Route::get('/', [KnowledgeApiController::class, 'index']);
        Route::post('/store', [KnowledgeApiController::class, 'store']);
    });
});
