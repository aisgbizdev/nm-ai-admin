@extends('layouts.app')

@section('title', __('Edit Admin'))

@section('header', __('Edit Admin'))

@section('content')
    <div class="p-6 space-y-6" x-data="{ showDiscard: false, showConfirm: false }" x-cloak>
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Edit Admin</h1>
                <p class="text-sm text-gray-500 mt-1">Perbarui data admin.</p>
            </div>
            <button type="button"
                class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm"
                @click="showDiscard = true">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </button>
        </div>

        <div class="mx-auto bg-white border border-gray-100 rounded-xl shadow-sm p-6">
            <form action="{{ route('admin.update', $admin->id) }}" method="POST" class="space-y-5" x-ref="editForm">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-gray-700" for="name">Nama</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $admin->name) }}" required
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300">
                        @error('name')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-gray-700" for="email">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $admin->email) }}" required
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300">
                        @error('email')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-gray-700" for="role">Role</label>
                        <select id="role" name="role" required
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300">
                            <option value="">Pilih role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role }}" @selected(old('role', $admin->role) === $role)>{{ $role }}</option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-gray-700" for="password">Password</label>
                        <input id="password" name="password" type="password" placeholder="Biarkan kosong jika tidak diganti"
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300">
                        @error('password')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="verified" name="verified" value="1"
                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        @checked(old('verified', $admin->email_verified_at ? true : false))>
                    <label for="verified" class="text-sm text-gray-700">Tandai email sudah terverifikasi</label>
                </div>

                <div class="flex gap-2 justify-end">
                    <button type="button"
                        class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                        @click="showDiscard = true">
                        Batal
                    </button>
                    <button type="button"
                        class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700"
                        @click="showConfirm = true">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        {{-- Modal konfirmasi discard --}}
        <template x-teleport="body">
            <div x-show="showDiscard" x-transition.opacity
                class="fixed inset-0 z-[9999] bg-black/50 flex items-center justify-center p-4"
                @click.self="showDiscard=false" @keydown.escape.window="showDiscard=false">
                <div x-show="showDiscard" x-transition
                    class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 space-y-4" role="dialog" aria-modal="true">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Batalkan perubahan?</h2>
                            <p class="text-sm text-gray-600 mt-1">Perubahan yang belum disimpan akan hilang.</p>
                        </div>
                        <button class="text-gray-500 hover:text-gray-700" @click="showDiscard=false" aria-label="Tutup modal">
                            &times;
                        </button>
                    </div>
                    <div class="flex flex-col sm:flex-row justify-end gap-2">
                        <button
                            class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                            @click="showDiscard=false">
                            Kembali
                        </button>
                        <a href="{{ route('admin.index') }}"
                            class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-semibold hover:bg-red-700 text-center">
                            Ya, batalkan
                        </a>
                    </div>
                </div>
            </div>
        </template>

        {{-- Modal sukses --}}
        <template x-teleport="body">
            <div x-show="showConfirm" x-transition.opacity
                class="fixed inset-0 z-[9999] bg-black/50 flex items-center justify-center p-4"
                @click.self="showConfirm=false" @keydown.escape.window="showConfirm=false">
                <div x-show="showConfirm" x-transition
                    class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 space-y-4" role="dialog" aria-modal="true">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Simpan perubahan?</h2>
                            <p class="text-sm text-gray-600 mt-1">Pastikan data sudah benar sebelum disimpan.</p>
                        </div>
                        <button class="text-gray-500 hover:text-gray-700" @click="showConfirm=false" aria-label="Tutup modal">
                            &times;
                        </button>
                    </div>
                    <div class="flex flex-col sm:flex-row justify-end gap-2">
                        <button
                            class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                            @click="showConfirm=false">
                            Periksa lagi
                        </button>
                        <button
                            class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700"
                            @click="$refs.editForm.submit()">
                            Ya, simpan
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endsection
