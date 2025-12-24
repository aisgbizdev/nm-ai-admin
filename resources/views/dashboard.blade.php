@extends('layouts.app')

@section('title', __('Dashboard'))

@section('header', __('Dashboard'))

@section('content')
    <div class="py-6 space-y-6">
        <div class="mx-auto px-6 space-y-4">
            <div class="flex flex-col gap-2">
                <p class="text-sm text-gray-500">Selamat datang kembali,</p>
                <div class="flex flex-col gap-1">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $user->name ?? 'User' }}</h1>
                    <div class="flex gap-3 items-center">
                        <span class="text-xs">
                            {{ $user->username ?? 'Username tidak tersedia' }}
                        </span>
                        <span>-</span>
                        <span
                            class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                            {{ $user->role ?? 'Role tidak tersedia' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                @foreach ($stats as $stat)
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4 flex flex-col gap-3">
                        <div class="flex items-center justify-between">
                            <div
                                class="w-10 h-10 rounded-lg flex items-center justify-center {{ $stat['color'] }} shadow-inner">
                                <i class="{{ $stat['icon'] }}"></i>
                            </div>
                            <span class="text-xs font-semibold text-emerald-600">{{ $stat['trend'] }}</span>
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm text-gray-500">{{ $stat['title'] }}</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stat['value'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mx-auto px-6 grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="xl:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-4 sm:p-6 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase text-gray-500 tracking-wide">
                                {{ $isSuperadmin ?? false ? 'Aktivitas Pengguna' : 'Perubahan Knowledge' }}
                            </p>
                            <h2 class="text-lg font-semibold text-gray-900">Terbaru</h2>
                        </div>
                        <a href="{{ $isSuperadmin ?? false ? route('history.index') : route('knowledge.index') }}"
                            class="text-sm font-semibold text-blue-600 hover:text-blue-700 focus:outline-none flex items-center gap-2">
                            Lihat semua
                            <i class="fa-solid fa-arrow-right-long text-xs"></i>
                        </a>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach ($activities as $activity)
                            <div class="flex items-start justify-between p-4 sm:p-6">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $activity['title'] }}</p>
                                    <p class="text-sm text-gray-500">{{ $activity['time'] }}</p>
                                </div>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">
                                    {{ $activity['status'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-4 sm:p-6 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase text-gray-500 tracking-wide">Aksi Cepat</p>
                            <h2 class="text-lg font-semibold text-gray-900">Mulai Bekerja</h2>
                        </div>
                    </div>
                    <div class="p-4 sm:p-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @if ($isSuperadmin ?? false)
                            <a href="{{ route('knowledge.create') }}" class="group">
                                <div
                                    class="rounded-lg border border-gray-100 p-4 flex flex-col gap-3 bg-gray-50 hover:bg-blue-50 transition">
                                    <div
                                        class="w-10 h-10 rounded-md bg-blue-100 text-blue-700 flex items-center justify-center">
                                        <i class="fa-solid fa-plus"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 group-hover:text-blue-700">Buat Knowledge</p>
                                        <p class="text-sm text-gray-500">Tambah artikel atau referensi baru.</p>
                                    </div>
                                </div>
                            </a>
                            <a href="{{ route('admin.index') }}" class="group">
                                <div
                                    class="rounded-lg border border-gray-100 p-4 flex flex-col gap-3 bg-gray-50 hover:bg-blue-50 transition">
                                    <div
                                        class="w-10 h-10 rounded-md bg-emerald-100 text-emerald-700 flex items-center justify-center">
                                        <i class="fa-solid fa-users-gear"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 group-hover:text-blue-700">Kelola Admin</p>
                                        <p class="text-sm text-gray-500">Tambah, edit, atau nonaktifkan admin.</p>
                                    </div>
                                </div>
                            </a>
                            <a href="{{ route('history.index') }}" class="group">
                                <div
                                    class="rounded-lg border border-gray-100 p-4 flex flex-col gap-3 bg-gray-50 hover:bg-blue-50 transition">
                                    <div
                                        class="w-10 h-10 rounded-md bg-amber-100 text-amber-700 flex items-center justify-center">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 group-hover:text-blue-700">Lihat History</p>
                                        <p class="text-sm text-gray-500">Audit percakapan terakhir.</p>
                                    </div>
                                </div>
                            </a>
                        @else
                            <a href="{{ route('knowledge.create') }}" class="group">
                                <div
                                    class="rounded-lg border border-gray-100 p-4 flex flex-col gap-3 bg-gray-50 hover:bg-blue-50 transition">
                                    <div
                                        class="w-10 h-10 rounded-md bg-blue-100 text-blue-700 flex items-center justify-center">
                                        <i class="fa-solid fa-plus"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 group-hover:text-blue-700">Buat Knowledge</p>
                                        <p class="text-sm text-gray-500">Tambahkan konten baru.</p>
                                    </div>
                                </div>
                            </a>
                            <a href="{{ route('knowledge.suggestions') }}" class="group">
                                <div
                                    class="rounded-lg border border-gray-100 p-4 flex flex-col gap-3 bg-gray-50 hover:bg-blue-50 transition">
                                    <div
                                        class="w-10 h-10 rounded-md bg-emerald-100 text-emerald-700 flex items-center justify-center">
                                        <i class="fa-solid fa-inbox"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 group-hover:text-blue-700">Review Suggestion
                                        </p>
                                        <p class="text-sm text-gray-500">Approve/reject usulan.</p>
                                    </div>
                                </div>
                            </a>
                            <a href="{{ route('history.index') }}" class="group">
                                <div
                                    class="rounded-lg border border-gray-100 p-4 flex flex-col gap-3 bg-gray-50 hover:bg-blue-50 transition">
                                    <div
                                        class="w-10 h-10 rounded-md bg-amber-100 text-amber-700 flex items-center justify-center">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 group-hover:text-blue-700">History Chat</p>
                                        <p class="text-sm text-gray-500">Lihat log percakapan.</p>
                                    </div>
                                </div>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-4 sm:p-6 border-b border-gray-100">
                        <p class="text-xs uppercase text-gray-500 tracking-wide">Insight</p>
                        <h2 class="text-lg font-semibold text-gray-900">Distribusi</h2>
                    </div>
                    <div class="p-4 sm:p-6 space-y-8">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs uppercase text-gray-500 tracking-wide">Knowledge Source</p>
                                    <h3 class="text-base font-semibold text-gray-900">Distribusi Sumber</h3>
                                </div>
                                <span class="text-xs font-semibold text-gray-500">{{ $knowledgeSources->sum('total') }}
                                    entri</span>
                            </div>

                            <div class="space-y-3">
                                @forelse ($knowledgeSources as $source)
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-900">{{ $source['label'] }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">{{ $source['total'] }} entri</p>
                                                </div>
                                            </div>
                                            <span
                                                class="text-sm font-semibold text-indigo-600">{{ $source['percent'] }}%</span>
                                        </div>
                                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-indigo-500" style="width: {{ $source['percent'] }}%">
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">Belum ada data sumber knowledge.</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs uppercase text-gray-500 tracking-wide">API Requests</p>
                                    <h3 class="text-base font-semibold text-gray-900">GET vs POST
                                        ({{ $apiRequests['range_label'] }})</h3>
                                </div>
                                <span class="text-xs font-semibold text-gray-500">{{ $apiRequests['total'] }}
                                    request</span>
                            </div>

                            <div class="space-y-3">
                                @forelse ($apiRequests['methods'] as $method)
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-900">{{ $method['method'] }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">{{ $method['count'] }} request</p>
                                                </div>
                                            </div>
                                            <span
                                                class="text-sm font-semibold text-emerald-600">{{ $method['percent'] }}%</span>
                                        </div>
                                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-emerald-500" style="width: {{ $method['percent'] }}%">
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">Belum ada aktivitas API.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
