@extends('layouts.app')

@section('title', __('Profile'))

@section('header', __('Profile'))

@section('content')
    <div class="relative isolate overflow-hidden">
        <div class="mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
            <div
                class="bg-white/80 backdrop-blur-lg border border-white/40 shadow-xl rounded-2xl p-6 sm:p-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-2">
                    <p class="text-sm font-semibold text-indigo-600 uppercase tracking-widest">{{ __('Akun Anda') }}</p>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ __('Kelola Profil') }}</h2>
                    <p class="text-gray-600">
                        {{ __('Perbarui informasi pribadi, sandi, dan kontrol akun Anda dari satu tempat.') }}</p>
                </div>
                <div
                    class="flex items-center gap-3 bg-indigo-50 text-indigo-700 px-4 py-3 rounded-xl border border-indigo-100 shadow-sm">
                    <div
                        class="h-12 w-12 flex items-center justify-center rounded-full bg-indigo-600 text-white text-xl font-semibold">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name ?? __('Pengguna') }}</p>
                        <p class="text-xs text-gray-600">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div id="profile-info"
                        class="bg-white/90 backdrop-blur-lg border border-white/40 shadow-lg rounded-2xl p-4 sm:p-8">
                        @include('profile.partials.update-profile-information-form')
                    </div>

                    <div id="security"
                        class="bg-white/90 backdrop-blur-lg border border-white/40 shadow-lg rounded-2xl p-4 sm:p-8">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <div class="space-y-6">
                    <div id="account-control"
                        class="bg-white/90 h-fit backdrop-blur-lg border border-white/40 shadow-lg rounded-2xl p-4 sm:p-8">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 space-y-6">
            @csrf
            @method('delete')

            <div class="space-y-2">
                <div
                    class="inline-flex items-center gap-2 rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700 border border-rose-100">
                    <span class="h-2 w-2 rounded-full bg-rose-500 animate-pulse"></span>
                    {{ __('Tidak dapat dipulihkan') }}
                </div>
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ __('Anda yakin ingin menghapus akun?') }}
                </h2>
                <p class="text-sm text-gray-600">
                    {{ __('Tindakan ini permanen. Semua data, konten, dan pengaturan akan dihapus tanpa bisa dikembalikan.') }}
                </p>
                <ul class="mt-2 space-y-1 text-sm text-gray-600 list-disc list-inside">
                    <li>{{ __('Sesi aktif akan ditutup secara otomatis.') }}</li>
                    <li>{{ __('Data yang terhapus tidak dapat dipulihkan.') }}</li>
                    <li>{{ __('Butuh verifikasi sandi untuk melanjutkan.') }}</li>
                </ul>
            </div>

            <div class="space-y-2" x-data="{ showPassword: false }">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <div class="relative">
                    <x-text-input id="password" name="password" x-bind:type="showPassword ? 'text' : 'password'"
                        class="mt-1 block w-full pr-12" placeholder="{{ __('Masukkan sandi untuk konfirmasi') }}"
                        required />
                    <button type="button"
                        class="absolute inset-y-0 right-3 inline-flex items-center text-gray-500 hover:text-gray-700"
                        x-on:click="showPassword = !showPassword" aria-label="{{ __('Tampilkan sandi') }}">
                        <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M2.5 12s3.5-6.5 9.5-6.5S21.5 12 21.5 12s-3.5 6.5-9.5 6.5S2.5 12 2.5 12z" />
                            <circle cx="12" cy="12" r="2.5" />
                        </svg>
                        <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M3 3l18 18M10.477 10.485A2.5 2.5 0 0112 9.5c2.25 0 4 2.5 4 2.5a8.978 8.978 0 01-1.518 2.258m-2.42 1.429A8.444 8.444 0 0112 15.5c-6 0-9.5-6-9.5-6a17.54 17.54 0 013.317-3.835" />
                        </svg>
                    </button>
                </div>

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Batal') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Hapus Permanen') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
@endsection
