@extends('layouts.app')

@section('title', __('Admin'))
@section('header', __('Admin'))

@section('content')
    <div class="p-6 space-y-6" x-data="{
        showDelete: false,
        targetId: null,
        targetName: '',
        openDelete(id, name) {
            this.targetId = id;
            this.targetName = name;
            this.showDelete = true;
            this.$nextTick(() => {
                this.$refs.deleteForm.action = '{{ route('admin.destroy', '__ID__') }}'.replace('__ID__', this.targetId);
                this.$refs.confirmBtn.focus();
            });
        },
        closeDelete() {
            this.showDelete = false;
        }
    }" x-cloak>
        {{-- Header --}}
        <div
            class="bg-white/80 backdrop-blur-lg border border-white/40 shadow-xl rounded-2xl p-6 sm:p-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-2">
                <p class="text-sm font-semibold text-indigo-600 uppercase tracking-widest">{{ __('Admin') }}</p>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Manajemen Admin</h1>
                <p class="text-sm text-gray-600">Ringkasan akses dan kontrol akun admin.</p>
            </div>
            <div class="flex flex-wrap gap-2 w-full lg:w-auto">
                <a href="{{ route('admin.export') }}"
                    class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm w-full sm:w-auto">
                    <i class="fa-solid fa-download"></i>
                    Export CSV
                </a>
                <a href="{{ route('admin.create') }}"
                    class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-indigo-600 text-white rounded-xl text-sm font-semibold hover:bg-indigo-700 shadow-sm w-full sm:w-auto">
                    <i class="fa-solid fa-plus"></i>
                    Tambah Admin
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-4">
            {{-- Table --}}
            <div class="xl:col-span-3 bg-white border border-gray-100 rounded-xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">Nama</th>
                                <th class="px-4 py-3 text-left font-semibold">Username</th>
                                <th class="px-4 py-3 text-left font-semibold">Email</th>
                                <th class="px-4 py-3 text-left font-semibold">Role</th>
                                <th class="px-4 py-3 text-left font-semibold">Status</th>
                                <th class="px-4 py-3 text-left font-semibold">Dibuat</th>
                                <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @foreach ($admins as $admin)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-semibold text-gray-900">{{ $admin['name'] }}</td>
                                    <td class="px-4 py-3 text-gray-800">{{ $admin['username'] ?? '-' }}</td>
                                    <td class="px-4 py-3 text-gray-800">{{ $admin['email'] }}</td>
                                    <td class="px-4 py-3">
                                        <span @class([
                                            'inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold border',
                                            'bg-indigo-50 text-indigo-600 border-indigo-100' =>
                                                $admin['role'] === 'Superadmin',
                                            'bg-blue-50 text-blue-600 border-blue-100' => $admin['role'] === 'Admin',
                                        ])>
                                            <span @class([
                                                'inline-block h-2 w-2 rounded-full',
                                                'bg-indigo-400' => $admin['role'] === 'Superadmin',
                                                'bg-blue-400' => $admin['role'] === 'Admin',
                                            ])></span>
                                            {{ $admin['role'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-800">{{ $admin['status'] }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $admin['created_at'] }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="inline-flex gap-2 text-xs">
                                            <a href="{{ route('admin.edit', $admin['id']) }}"
                                                class="px-3 py-2 rounded-lg border border-gray-200 text-gray-800 hover:bg-gray-50">
                                                Edit
                                            </a>

                                            @if ($admin['is_self'])
                                                <button type="button"
                                                    class="px-3 py-2 rounded-lg border border-gray-100 text-gray-400 cursor-not-allowed">
                                                    Hapus
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="px-3 py-2 rounded-lg border border-rose-200 text-rose-700 hover:bg-rose-50"
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
                <div class="px-4 py-3 border-t border-gray-100 bg-white">
                    {{ $admins->links('vendor.pagination.tailwind') }}
                </div>
            </div>

            {{-- Summary --}}
            <div class="space-y-3">
                <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-4">
                    <p class="text-sm text-gray-600">Total Admin</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ $totalAdmins }}</p>
                    <p class="text-xs text-gray-500">Semua akun terdaftar</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-4">
                    <p class="text-sm text-gray-600">Aktif</p>
                    <p class="mt-1 text-2xl font-bold text-emerald-700">{{ $activeAdmins }}</p>
                    <p class="text-xs text-gray-500">Email terverifikasi</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-4">
                    <p class="text-sm text-gray-600">Menunggu</p>
                    <p class="mt-1 text-2xl font-bold text-amber-700">{{ $pendingAdmins }}</p>
                    <p class="text-xs text-gray-500">Belum verifikasi email</p>
                </div>
            </div>
        </div>

        {{-- Delete Modal --}}
        <template x-teleport="body">
            <div x-show="showDelete" x-transition.opacity
                class="fixed inset-0 z-[9999] bg-black/50 flex items-center justify-center p-4"
                @click.self="closeDelete()" @keydown.escape.window="if(showDelete) closeDelete()">
                <div x-show="showDelete" x-transition class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 space-y-4"
                    role="dialog" aria-modal="true">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Hapus admin?</h2>
                            <p class="text-sm text-gray-600 mt-1">Anda akan menghapus <span class="font-semibold"
                                    x-text="targetName"></span>.</p>
                        </div>
                        <button class="text-gray-400 hover:text-gray-600" @click="closeDelete()" aria-label="Tutup modal">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
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
                                class="px-4 py-2 rounded-lg bg-rose-600 text-white text-sm font-semibold hover:bg-rose-700">
                                Ya, hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endsection
