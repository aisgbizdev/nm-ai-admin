<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use SplFileInfo;

class ApiLogController extends Controller
{
    public function index(): View
    {
        $path = storage_path('logs');

        /** @var Collection<int, array{name: string, size: int, modified_at: int}> $files */
        $files = collect(File::files($path))
            ->filter(fn (SplFileInfo $file) => str_contains($file->getFilename(), 'api-'))
            ->sortByDesc(fn (SplFileInfo $file) => $file->getMTime())
            ->values()
            ->map(fn (SplFileInfo $file) => [
                'name' => $file->getFilename(),
                'size' => $file->getSize(),
                'modified_at' => $file->getMTime(),
            ]);

        return view('api-log.index', compact('files'));
    }

    public function show(string $file): View
    {
        $file = basename($file);

        $filePath = storage_path("logs/{$file}");

        abort_if(! File::exists($filePath), 404);

        $fileInfo = new SplFileInfo($filePath);

        $lines = collect(file($filePath))
            ->reverse()
            ->take(300);

        return view('api-log.show', [
            'file' => $file,
            'meta' => [
                'name' => $fileInfo->getFilename(),
                'size' => $fileInfo->getSize(),
                'modified_at' => $fileInfo->getMTime(),
                'path' => $fileInfo->getPathname(),
            ],
            'lines' => $lines,
        ]);
    }
}
