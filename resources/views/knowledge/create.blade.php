@extends('layouts.app')

@section('title', __('Tambah Knowledge'))
@section('header', __('Tambah Knowledge'))

@section('content')
    <div class="p-6">
        <div class="space-y-6">
            <div
                class="bg-white/80 backdrop-blur border border-white/60 shadow-xl rounded-2xl p-6 sm:p-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    <p class="text-xs font-semibold text-indigo-600 uppercase tracking-[0.2em]">Knowledge</p>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Tambah Knowledge</h1>
                    <p class="text-sm text-gray-600">Isi pertanyaan, jawaban markdown, sumber, lalu publish.</p>
                </div>
                <a href="{{ route('knowledge.index') }}"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm">
                    <i class="fa-solid fa-arrow-left"></i>
                    Kembali
                </a>
            </div>

            <form action="{{ route('knowledge.store') }}" method="POST"
                class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 sm:p-8 space-y-6">
                @csrf

                <div class="m-0">
                    <label class="text-sm font-semibold text-gray-800" for="title">Judul</label>
                    <input id="title" name="title" type="text" value="{{ old('title') }}"
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
                    <textarea name="answer" class="hidden">{{ old('answer', '') }}</textarea>

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
                                <option value="{{ $src }}" @selected(old('source') === $src)>{{ ucfirst($src) }}
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
                            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', true))
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            Publish sekarang
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
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
