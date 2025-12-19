@extends('layouts.app')

@section('title', __('Admin'))
@section('header', __('Admin'))

@section('content')
    <div class="relative isolate overflow-hidden" x-data="{
        showDelete: false,
        targetId: null,
        targetName: '',
        showExport: false,
        openDelete(id, name) {
            this.targetId = id;
            this.targetName = name;
            this.showDelete = true;
            document.body.classList.add('overflow-hidden');
    
            this.$nextTick(() => {
                this.$refs.deleteForm.action = '{{ route('admin.destroy', '__ID__') }}'.replace('__ID__', this.targetId);
                this.$refs.confirmBtn.focus();
            });
        },
        closeDelete() {
            this.showDelete = false;
            document.body.classList.remove('overflow-hidden');
        }
    }" x-cloak>
        <div class="p-6 space-y-8">
            {{-- Header --}}
            <div
                class="bg-white/80 backdrop-blur-lg border border-white/40 shadow-xl rounded-2xl p-6 sm:p-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="space-y-2">
                    <p class="text-sm font-semibold text-indigo-600 uppercase tracking-widest">{{ __('Admin') }}</p>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Manajemen Admin</h1>
                    <p class="text-sm text-gray-600">Ringkasan akses dan kontrol akun admin.</p>
                </div>
                <div class="flex flex-wrap gap-2 w-full lg:w-auto">
                    <button type="button"
                        class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm w-full sm:w-auto"
                        @click="showExport = true; setTimeout(() => { window.location = '{{ route('admin.export') }}'; }, 200); setTimeout(() => showExport=false, 1500);">
                        <i class="fa-solid fa-download"></i>
                        Export CSV
                    </button>
                    <a href="{{ route('admin.create') }}"
                        class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-indigo-600 text-white rounded-xl text-sm font-semibold hover:bg-indigo-700 shadow-sm w-full sm:w-auto">
                        <i class="fa-solid fa-plus"></i>
                        Tambah Admin
                    </a>
                </div>
            </div>

            {{-- Stat cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white/90 backdrop-blur-lg border border-white/40 rounded-2xl shadow-lg p-5">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-gray-600">Total Admin</p>
                        <span
                            class="h-8 w-8 inline-flex items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
                            <i class="fa-solid fa-users"></i>
                        </span>
                    </div>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $totalAdmins }}</p>
                    <p class="text-xs text-gray-500 mt-1">Semua akun terdaftar</p>
                </div>
            <div class="bg-white/90 backdrop-blur-lg border border-white/40 rounded-2xl shadow-lg p-5">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-gray-600">Aktif</p>
                        <span
                            class="h-8 w-8 inline-flex items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                            <i class="fa-solid fa-circle-check"></i>
                        </span>
                    </div>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $activeAdmins }}</p>
                    <p class="text-xs text-gray-500 mt-1">Email terverifikasi</p>
                </div>
            <div class="bg-white/90 backdrop-blur-lg border border-white/40 rounded-2xl shadow-lg p-5">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-gray-600">Menunggu</p>
                        <span
                            class="h-8 w-8 inline-flex items-center justify-center rounded-full bg-amber-50 text-amber-600">
                            <i class="fa-solid fa-clock"></i>
                        </span>
                    </div>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $pendingAdmins }}</p>
                    <p class="text-xs text-gray-500 mt-1">Belum verifikasi email</p>
                </div>
            </div>

            {{-- Table --}}
            <div class="bg-white/90 backdrop-blur-lg border border-white/40 rounded-2xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    Admin</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    Email</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    Role</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    Tanggal dibuat</th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @foreach ($admins as $admin)
                                <tr class="hover:bg-indigo-50/60">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-semibold">
                                                {{ strtoupper(substr($admin['name'], 0, 1)) }}
                                            </div>
                                            <div class="min-w-[120px]">
                                                <p class="font-semibold text-gray-900 break-words">{{ $admin['name'] }}</p>
                                                <p class="text-xs text-gray-500">ID: {{ $admin['id'] }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <span
                                            class="px-2 py-1 rounded-lg font-medium text-gray-800 bg-gray-50 border border-gray-200">
                                            {{ $admin['email'] }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3">
                                        <span @class([
                                            'inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold border',
                                            'bg-indigo-50 text-indigo-500 border-indigo-100' =>
                                                $admin['role'] === 'Superadmin',
                                            'bg-blue-50 text-blue-500 border-blue-100' => $admin['role'] === 'Admin',
                                        ])>
                                            <span @class([
                                                'inline-block h-2 w-2 rounded-full',
                                                'bg-indigo-200' => $admin['role'] === 'Superadmin',
                                                'bg-blue-200' => $admin['role'] === 'Admin',
                                            ])></span>

                                            {{ $admin['role'] }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3">
                                        <p class="text-sm text-gray-700">{{ $admin['created_at'] }}</p>
                                    </td>

                                    <td class="px-4 py-3 text-right">
                                        <div class="inline-flex w-full gap-2 items-center text-xs">
                                            <a href="{{ route('admin.edit', $admin['id']) }}"
                                                class="w-full px-3 py-2 rounded-lg font-semibold text-center border border-indigo-100 text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition-all">
                                                Edit
                                            </a>

                                            @if ($admin['is_self'])
                                                <button type="button"
                                                    class="w-full px-3 py-2 rounded-lg font-semibold border border-gray-100 bg-gray-50 text-gray-300 cursor-not-allowed">
                                                    Hapus
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="w-full px-3 py-2 rounded-lg font-semibold border border-rose-100 text-rose-700 bg-rose-50 hover:bg-rose-100 transition-all"
                                                    @click="openDelete({{ $admin['id'] }}, @js($admin['name']))">
                                                    Hapus
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
                <div class="px-4 py-3 border-t border-gray-100 bg-white/70 backdrop-blur">
                    {{ $admins->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        </div>

        {{-- バ. Modal (FIX GAP) --}}
        <template x-teleport="body">
            <div x-show="showDelete" x-transition.opacity
                class="fixed inset-0 z-[9999] bg-black/50 flex items-center justify-center p-4" @click.self="closeDelete()"
                @keydown.escape.window="if(showDelete) closeDelete()">
                <div x-show="showDelete" x-transition
                    class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6 space-y-4 border border-white/70 backdrop-blur"
                    role="dialog" aria-modal="true">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div
                                class="inline-flex items-center gap-2 rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700 border border-rose-100">
                                <span class="h-2 w-2 rounded-full bg-rose-500 animate-pulse"></span>
                                Risiko Tinggi
                            </div>
                            <h2 class="text-xl font-semibold text-gray-900 mt-2">Hapus admin?</h2>
                            <p class="text-sm text-gray-600 mt-1">Tindakan ini akan menonaktifkan akses pengguna.</p>
                        </div>
                        <button class="text-gray-400 hover:text-gray-600" @click="closeDelete()" aria-label="Tutup modal">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <div class="bg-gray-50 border border-gray-100 rounded-lg p-4">
                        <p class="text-sm text-gray-700">
                            Anda akan menghapus admin:
                            <span class="font-semibold text-gray-900" x-text="targetName"></span>
                        </p>
                        <p class="text-xs text-gray-500 mt-2">Pastikan akses yang diperlukan sudah dipindahkan.</p>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-2">
                        <button
                            class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                            @click="closeDelete()">
                            Batal
                        </button>

                        <form method="POST" x-ref="deleteForm" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" x-ref="confirmBtn"
                                class="px-4 py-2 rounded-lg bg-rose-600 text-white text-sm font-semibold hover:bg-rose-700 w-full sm:w-auto">
                                Ya, hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </template>

        {{-- Modal export sukses --}}
        <template x-teleport="body">
            <div x-show="showExport" x-transition.opacity
                class="fixed inset-0 z-[9999] bg-black/50 flex items-center justify-center p-4">
                <div x-show="showExport" x-transition
                    class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-4 text-center border border-white/70 backdrop-blur"
                    role="alert">
                    <div class="flex flex-col items-center gap-2">
                        <div
                            class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <h2 class="text-lg font-semibold text-gray-900">Export dimulai</h2>
                        <p class="text-sm text-gray-600">File CSV sedang diproses, browser akan mengunduh otomatis.</p>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endsection
