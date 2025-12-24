@extends('layouts.app')

@section('title', 'API Log Detail')
@section('header', 'API Logs')

@section('content')
    <div class="py-6 space-y-8">
        <div class="mx-auto px-6">
            <div
                class="bg-white/80 backdrop-blur-lg border border-white/40 shadow-xl rounded-2xl p-6 sm:p-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="space-y-2">
                    <p class="text-sm font-semibold text-indigo-600 uppercase tracking-widest">Monitoring</p>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Detail API Log</h1>
                    <p class="text-sm text-gray-600">Menampilkan 300 baris terbaru (terbalik) dari file log.</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
                    <a href="{{ route('api.logs') }}"
                        class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm w-full sm:w-auto">
                        <i class="fa-solid fa-arrow-left"></i>
                        Kembali
                    </a>
                    <a href="{{ route('api.logs.show', $file) }}"
                        class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 shadow-sm w-full sm:w-auto">
                        <i class="fa-solid fa-rotate-right"></i>
                        Refresh
                    </a>
                </div>
            </div>
        </div>

        <div class="px-6 space-y-4">
            <div class="bg-white border border-gray-100 rounded-xl shadow-lg overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-gray-100 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div class="space-y-1">
                        <p class="text-xs uppercase text-gray-500 tracking-wide">File</p>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $meta['name'] }}</h3>
                        <div class="flex flex-wrap gap-3 text-sm text-gray-600">
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gray-50 border border-gray-200">
                                <i class="fa-regular fa-clock text-gray-500"></i>
                                {{ \Illuminate\Support\Carbon::createFromTimestamp($meta['modified_at'])->diffForHumans() }}
                            </span>
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gray-50 border border-gray-200">
                                <i class="fa-regular fa-file-lines text-gray-500"></i>
                                {{ number_format($meta['size'] / 1024, 1) }} KB
                            </span>
                        </div>
                    </div>

                    <div class="text-xs text-gray-500 break-all">
                        {{ $meta['path'] }}
                    </div>
                </div>

                <div class="bg-slate-950 text-slate-50 text-xs leading-6">
                    <div class="px-4 py-3 border-b border-slate-800 flex items-center justify-between text-slate-300">
                        <div class="flex items-center gap-3">
                            <div class="flex h-7 w-7 items-center justify-center rounded-sm bg-slate-700 text-slate-200">
                                <i class="fa-solid fa-terminal text-sm"></i>
                            </div>
                            <span class="text-sm font-mono font-medium text-slate-200">
                                Log Output <span class="text-slate-400">(terbaru → terlama)</span>
                            </span>
                        </div>
                        <span class="text-slate-400">menampilkan max 300 baris</span>
                    </div>

                    <div class="max-h-[640px] overflow-auto">
                        <table class="min-w-full text-left">
                            <thead class="bg-slate-900 text-slate-400 uppercase text-[11px]">
                                <tr>
                                    <th class="px-4 py-2 font-semibold w-16">#</th>
                                    <th class="px-4 py-2 font-semibold">Log</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-900 font-mono">
                                @foreach ($lines as $line)
                                    <tr class="hover:bg-slate-900/60">
                                        <td class="px-4 py-2 align-top text-slate-500">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-2 whitespace-pre-wrap text-slate-50">{{ rtrim($line) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
