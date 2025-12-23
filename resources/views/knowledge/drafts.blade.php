@extends('layouts.app')

@section('title', __('Knowledge Drafts'))
@section('header', __('Knowledge Drafts'))

@section('content')
    <div class="py-6 space-y-6">
        <div class="mx-auto px-6">
            <div
                class="bg-white/80 backdrop-blur-lg border border-white/40 shadow-xl rounded-2xl p-6 sm:p-8 flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                <div class="space-y-2">
                    <p class="text-sm font-semibold text-indigo-600 uppercase tracking-widest">Drafts</p>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Draft Knowledge</h1>
                    <p class="text-sm text-gray-600">Daftar knowledge yang belum dipublish.</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-2 w-full xl:w-auto">
                    <a href="{{ route('knowledge.index') }}"
                        class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm w-full sm:w-auto">
                        <i class="fa-solid fa-arrow-left"></i>
                        Kembali
                    </a>
                    <a href="{{ route('knowledge.create') }}"
                        class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 shadow-sm w-full sm:w-auto">
                        <i class="fa-solid fa-plus"></i>
                        Tambah
                    </a>
                </div>
            </div>
        </div>

        <div class="mx-auto px-6">
            <div class="bg-white border border-gray-100 rounded-xl shadow-lg overflow-hidden">
                <div
                    class="p-4 sm:p-6 border-b border-gray-100 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-xs uppercase text-gray-500 tracking-wide">Drafts</p>
                        <h3 class="text-lg font-semibold text-gray-900">Belum Publish</h3>
                    </div>
                    <form method="GET" action="{{ route('knowledge.drafts') }}"
                        class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
                        <input type="text" name="q" value="{{ request('q') }}"
                            class="w-full sm:w-64 px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 placeholder:text-gray-400 focus:ring-2 focus:ring-blue-200 focus:border-blue-300"
                            placeholder="Cari judul / isi / source..." />
                        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm w-full sm:w-auto">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                Cari
                            </button>
                            @if (request('q'))
                                <a href="{{ route('knowledge.drafts') }}"
                                    class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm w-full sm:w-auto">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">Judul</th>
                                <th class="px-4 py-3 text-left font-semibold">Source</th>
                                <th class="px-4 py-3 text-left font-semibold">Updated</th>
                                <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($drafts as $draft)
                                <tr class="hover:bg-gray-50/60">
                                    <td class="px-4 py-3 font-semibold text-gray-900">{{ $draft->title }}</td>
                                    <td class="px-4 py-3 text-gray-700">
                                        <span
                                            class="px-2 py-1 rounded-full bg-gray-100 text-xs font-semibold text-gray-700 uppercase">
                                            {{ ucfirst($draft->source ?? 'manual') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ optional($draft->updated_at)->diffForHumans() ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="inline-flex gap-2 text-xs">
                                            <a href="{{ route('knowledge.edit', $draft->id) }}"
                                                class="px-3 py-2 rounded-lg border border-gray-200 text-gray-800 hover:bg-gray-50">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('knowledge.approve', $draft->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    class="px-3 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">
                                                    Publish
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                        Belum ada draft.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-100">
                    {{ $drafts->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
