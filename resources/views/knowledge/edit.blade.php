@extends('layouts.app')

@section('title', __('Edit Knowledge'))
@section('header', __('Edit Knowledge'))

@section('content')
    <div class="p-6" x-data="{ showDelete: false }" x-cloak>
        <div class="max-w-5xl mx-auto space-y-6">
            <div
                class="bg-white/80 backdrop-blur border border-white/60 shadow-xl rounded-2xl p-6 sm:p-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    <p class="text-xs font-semibold text-indigo-600 uppercase tracking-[0.2em]">Knowledge</p>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Edit Knowledge</h1>
                    <p class="text-sm text-gray-600">Perbarui konten, sumber, dan status publikasi.</p>
                </div>
                <a href="{{ route('knowledge.index') }}"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm">
                    <i class="fa-solid fa-arrow-left"></i>
                    Kembali
                </a>
            </div>

            <form action="{{ route('knowledge.update', $entry->id) }}" method="POST"
                class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 sm:p-8 space-y-6">
                @csrf
                @method('PUT')

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-800" for="title">Judul</label>
                    <input id="title" name="title" type="text" value="{{ old('title', $entry->title) }}"
                        class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300"
                        placeholder="Contoh: Cara reset password akun">
                    @error('title')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-semibold text-gray-800">Jawaban (Markdown)</label>
                    </div>

                    {{-- editor UI --}}
                    <div data-tui-editor class="border border-gray-200 rounded-lg overflow-hidden"></div>

                    {{-- value yang dikirim ke Laravel (tetap markdown) --}}
                    <textarea name="answer" class="hidden">{{ old('answer', $entry->answer) }}</textarea>

                    <p class="text-xs text-gray-500">
                        Yang terlihat editor adalah tabel, tapi yang tersimpan di database tetap Markdown.
                    </p>

                    @error('answer')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-gray-800" for="source">Sumber</label>
                        <select id="source" name="source" required
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300">
                            @foreach (['manual', 'import', 'ai_generated', 'scrape', 'system'] as $src)
                                <option value="{{ $src }}" @selected(old('source', $entry->source) === $src)>{{ ucfirst($src) }}
                                </option>
                            @endforeach
                        </select>
                        @error('source')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2 md:col-span-2">
                        <label class="text-sm font-semibold text-gray-800">Publikasi</label>
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $entry->is_published))
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            Publish
                        </label>
                    </div>
                </div>

                <div class="flex gap-2 justify-end">
                    <a href="{{ route('knowledge.index') }}"
                        class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>

            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 sm:p-8">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Hapus Knowledge</p>
                        <p class="text-xs text-gray-500">Tindakan ini akan menonaktifkan data.</p>
                    </div>
                    <button type="button"
                        class="px-3 py-2 rounded-lg text-sm font-semibold border border-gray-200 text-red-600 hover:bg-red-50"
                        @click="showDelete = true">
                        Hapus
                    </button>
                </div>
            </div>
        </div>

        {{-- Modal delete --}}
        <template x-teleport="body">
            <div x-show="showDelete" x-transition.opacity
                class="fixed inset-0 z-[9999] bg-black/50 flex items-center justify-center p-4"
                @click.self="showDelete=false" @keydown.escape.window="showDelete=false">
                <div x-show="showDelete" x-transition class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 space-y-4"
                    role="dialog" aria-modal="true">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Hapus knowledge?</h2>
                            <p class="text-sm text-gray-600 mt-1">Data akan dinonaktifkan.</p>
                        </div>
                        <button class="text-gray-500 hover:text-gray-700" @click="showDelete=false"
                            aria-label="Tutup modal">
                            &times;
                        </button>
                    </div>
                    <div class="flex flex-col sm:flex-row justify-end gap-2">
                        <button
                            class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                            @click="showDelete=false">
                            Batal
                        </button>
                        <form action="{{ route('knowledge.destroy', $entry->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-semibold hover:bg-red-700">
                                Ya, hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endsection
