@extends('layouts.app')

@section('title', __('Profil'))

@section('header', __('Profil'))

@section('content')
    @php
        $userInitial = strtoupper(substr($user?->name ?? 'U', 0, 1));
        $isVerified = filled($user?->email_verified_at);
    @endphp

    <div class="relative isolate overflow-hidden">
        <div class="p-6 space-y-8">
            <div
                class="bg-white/80 backdrop-blur-lg border border-white/40 shadow-xl rounded-2xl p-6 sm:p-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="space-y-2">
                    <p class="text-sm font-semibold text-indigo-600 uppercase tracking-widest">{{ __('Profil') }}</p>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ __('Ringkasan Akun Anda') }}</h1>
                    <p class="text-gray-600">
                        {{ __('Lihat status akun secara ringkas lalu lanjutkan ke halaman pengaturan untuk melakukan perubahan.') }}
                    </p>
                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('profile.edit') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-semibold shadow hover:bg-indigo-700 transition">
                            <i class="fa-solid fa-pen"></i>
                            {{ __('Kelola Profil') }}
                        </a>
                        <a href="{{ route('profile.edit') }}#security"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-indigo-200 text-indigo-700 bg-indigo-50 text-sm font-semibold shadow-sm hover:bg-indigo-100 transition">
                            <i class="fa-solid fa-shield-halved"></i>
                            {{ __('Keamanan') }}
                        </a>
                    </div>
                </div>
                <div
                    class="flex items-center gap-3 bg-indigo-50 text-indigo-700 px-4 py-3 rounded-xl border border-indigo-100 shadow-sm">
                    <div
                        class="h-14 w-14 flex items-center justify-center rounded-full bg-indigo-600 text-white text-2xl font-semibold">
                        {{ $userInitial }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $user?->name ?? __('Pengguna') }}</p>
                        <p class="text-xs text-gray-600">{{ $user?->email ?? '-' }}</p>
                        <div class="mt-1 inline-flex items-center gap-1 text-xs font-semibold rounded-full"
                            @class([
                                'bg-emerald-50 text-emerald-700 border border-emerald-100' => $isVerified,
                                'bg-amber-50 text-amber-700 border border-amber-100' => !$isVerified,
                            ])>
                            {{ $isVerified ? __('Email terverifikasi') : __('Email belum diverifikasi') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white/90 backdrop-blur-lg border border-white/40 shadow-lg rounded-2xl p-6 sm:p-8">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">{{ __('Ringkasan Akun') }}</h2>
                                <p class="text-sm text-gray-600">{{ __('Detail utama akun Anda.') }}</p>
                            </div>
                            <a href="{{ route('profile.edit') }}#profile-info"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-xl hover:bg-indigo-100 transition">
                                <i class="fa-solid fa-pen"></i>
                                {{ __('Ubah Data') }}
                            </a>
                        </div>

                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Nama') }}
                                </dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $user?->name ?? '-' }}</dd>
                            </div>
                            <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Email') }}
                                </dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $user?->email ?? '-' }}</dd>
                            </div>
                            <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Peran') }}
                                </dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $user?->role ?? __('User') }}</dd>
                            </div>
                            <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    {{ __('Bergabung') }}</dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900">
                                    {{ optional($user?->created_at)->format('d M Y') ?? '-' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="space-y-6">
                    <div
                        class="bg-white/90 backdrop-blur-lg border border-white/40 shadow-lg rounded-2xl p-6 sm:p-8 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-semibold text-gray-900">{{ __('Keamanan') }}</h3>
                            <span
                                class="px-3 py-1 text-xs font-semibold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100">
                                {{ __('Disarankan') }}
                            </span>
                        </div>
                        <ul class="space-y-2 text-sm text-gray-700">
                            <li class="flex items-start gap-2">
                                <span class="mt-1 h-2 w-2 rounded-full bg-indigo-400"></span>
                                {{ __('Perbarui sandi secara berkala untuk menjaga keamanan.') }}
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="mt-1 h-2 w-2 rounded-full bg-indigo-400"></span>
                                {{ __('Pastikan email Anda aktif untuk pemulihan akun.') }}
                            </li>
                        </ul>
                        <a href="{{ route('profile.edit') }}#security"
                            class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-700">
                            <i class="fa-solid fa-arrow-right"></i>
                            {{ __('Atur Keamanan') }}
                        </a>
                    </div>

                    <div
                        class="bg-white/90 backdrop-blur-lg border border-white/40 shadow-lg rounded-2xl p-6 sm:p-8 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-semibold text-gray-900">{{ __('Kontrol Akun') }}</h3>
                            <span
                                class="px-3 py-1 text-xs font-semibold rounded-full bg-rose-50 text-rose-700 border border-rose-100">
                                {{ __('Hati-hati') }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-700">
                            {{ __('Kelola penghapusan akun dan pahami bahwa tindakan ini bersifat permanen.') }}
                        </p>
                        <a href="{{ route('profile.edit') }}#account-control"
                            class="inline-flex items-center gap-2 text-sm font-semibold text-rose-700">
                            <i class="fa-solid fa-arrow-right"></i>
                            {{ __('Kelola Penghapusan') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
