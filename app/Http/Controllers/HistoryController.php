<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HistoryController extends Controller
{
    public function index(): View
    {
        return view('history.index');
    }

    public function show(string $sessionId): View
    {
        return view('history.show', ['sessionId' => $sessionId]);
    }
}
