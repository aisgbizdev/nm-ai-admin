<section class="space-y-6">
    <header class="space-y-2">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-rose-600">{{ __('Aksi Berisiko') }}</p>
        <h2 class="text-xl font-bold text-gray-900">
            {{ __('Hapus Akun') }}
        </h2>
        <p class="text-sm text-gray-600">
            {{ __('Menghapus akun akan menghilangkan seluruh data secara permanen. Unduh salinan data penting sebelum melanjutkan.') }}
        </p>
    </header>

    <x-danger-button class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold" x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M12 9v4m0 4h.01M5.26 5.26a9 9 0 1113.48 13.48A9 9 0 015.26 5.26z" />
        </svg>
        {{ __('Hapus Akun') }}
    </x-danger-button>
</section>
