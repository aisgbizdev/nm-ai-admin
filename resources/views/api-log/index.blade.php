@extends('layouts.app')

@section('title', 'API Logs')
@section('header', 'API Logs')

@section('content')
    <div class="py-6 space-y-8">
        <div class="mx-auto px-6">
            <div
                class="bg-white/80 backdrop-blur-lg border border-white/40 shadow-xl rounded-2xl p-6 sm:p-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="space-y-2">
                    <p class="text-sm font-semibold text-indigo-600 uppercase tracking-widest">Monitoring</p>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">API Logs</h1>
                    <p class="text-sm text-gray-600">Lihat riwayat permintaan API yang tersimpan di storage.</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
                    <a href="{{ route('api.logs') }}"
                        class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm w-full sm:w-auto">
                        <i class="fa-solid fa-rotate-right"></i>
                        Refresh
                    </a>

                    @if ($files->isNotEmpty())
                        <a href="{{ route('api.logs.show', $files->first()['name']) }}"
                            class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 shadow-sm w-full sm:w-auto">
                            <i class="fa-solid fa-eye"></i>
                            Lihat Terbaru
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="px-6">
            <div class="bg-white border border-gray-100 rounded-xl shadow-lg overflow-hidden">
                <div
                    class="p-4 sm:p-6 border-b border-gray-100 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-xs uppercase text-gray-500 tracking-wide">Log Files</p>
                        <h3 class="text-lg font-semibold text-gray-900">Riwayat request API</h3>
                    </div>

                    <span
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 text-xs font-semibold text-indigo-700 border border-indigo-100">
                        <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                        {{ $files->count() }} file
                    </span>
                </div>

                @if ($files->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-gray-600">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold">Nama File</th>
                                    <th class="px-4 py-3 text-left font-semibold">Ukuran</th>
                                    <th class="px-4 py-3 text-left font-semibold">Diubah</th>
                                    <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">
                                @foreach ($files as $file)
                                    <tr class="hover:bg-gray-50/60">
                                        <td class="px-4 py-3">
                                            <div class="font-semibold text-gray-900">{{ $file['name'] }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-gray-600">
                                            {{ number_format($file['size'] / 1024, 1) }} KB
                                        </td>
                                        <td class="px-4 py-3 text-gray-600">
                                            {{ \Illuminate\Support\Carbon::createFromTimestamp($file['modified_at'])->diffForHumans() }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex justify-end">
                                                <a href="{{ route('api.logs.show', $file['name']) }}"
                                                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white border border-gray-200 text-gray-700 text-xs font-semibold hover:bg-gray-50">
                                                    <i class="fa-solid fa-eye"></i>
                                                    Lihat
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center bg-gray-50">
                        <div
                            class="mx-auto h-12 w-12 rounded-2xl bg-white border border-dashed border-gray-200 text-gray-500 flex items-center justify-center">
                            <i class="fa-regular fa-file-lines"></i>
                        </div>
                        <p class="mt-4 text-base font-semibold text-gray-800">Belum ada log API</p>
                        <p class="mt-1 text-sm text-gray-600">Jalankan request API untuk menghasilkan log baru.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
