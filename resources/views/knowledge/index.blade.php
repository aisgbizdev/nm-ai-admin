@extends('layouts.app')

@section('title', __('Knowledge'))
@section('header', __('Knowledge'))

@section('content')
    <div class="py-6 space-y-8">
        {{-- HEADER --}}
        <div class="mx-auto px-6">
            <div
                class="bg-white/80 backdrop-blur-lg border border-white/40 shadow-xl rounded-2xl p-6 sm:p-8 flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                <div class="space-y-2">
                    <p class="text-sm font-semibold text-indigo-600 uppercase tracking-widest">{{ __('Knowledge') }}</p>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Knowledge Base</h1>
                    <p class="text-sm text-gray-600">Kumpulan artikel & panduan (manual approve).</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-2 w-full xl:w-auto">
                    <a href="{{ route('knowledge.suggestions') }}"
                        class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm w-full sm:w-auto">
                        <i class="fa-solid fa-inbox"></i>
                        Suggestions
                    </a>

                    <a href="{{ route('knowledge.create') }}"
                        class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 shadow-sm w-full sm:w-auto">
                        <i class="fa-solid fa-plus"></i>
                        Tambah
                    </a>
                </div>
            </div>
        </div>

        {{-- COLLECTIONS GRID (Source Groups) --}}
        <div class="mx-auto px-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            @forelse ($collections as $c)
                <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-lg flex flex-col justify-between gap-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-2">
                            <span
                                class="w-10 h-10 rounded-lg flex items-center justify-center {{ $c['color'] ?? 'bg-blue-50 text-blue-700' }} shadow-inner">
                                <i class="fa-solid fa-layer-group"></i>
                            </span>
                            <div>
                                <h2 class="font-semibold text-gray-900 uppercase leading-tight">
                                    {{ $c['title'] ?? '-' }}
                                </h2>
                                <p class="text-sm text-gray-500">
                                    {{ $c['desc'] ?? '-' }}
                                </p>
                            </div>
                        </div>

                        <span class="text-xs font-semibold text-gray-500">
                            {{ $c['updated'] ?? '-' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span class="text-xs font-semibold text-gray-500">Total dokumen</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $c['count'] ?? 0 }}×</span>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                    <p class="text-sm text-gray-600">Belum ada data sumber.</p>
                </div>
            @endforelse
        </div>

        {{-- RECENT + SUMMARY --}}
        <div class="mx-auto px-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white border border-gray-100 rounded-xl shadow-lg">
                <div class="p-4 sm:p-6 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase text-gray-500 tracking-wide">Artikel Terbaru</p>
                        <h3 class="text-lg font-semibold text-gray-900">Baru ditambahkan</h3>
                    </div>
                    <a href="#manage"
                        class="text-sm font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-2">
                        Kelola
                        <i class="fa-solid fa-arrow-right-long text-xs"></i>
                    </a>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse ($recent as $item)
                        <div class="p-4 sm:p-6 flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $item['title'] ?? '-' }}</p>
                                <p class="text-sm text-gray-500">Sumber {{ $item['owner'] ?? '-' }}</p>
                            </div>
                            <span class="text-sm text-gray-500">{{ $item['time'] ?? '-' }}</span>
                        </div>
                    @empty
                        <div class="p-4 sm:p-6">
                            <p class="text-sm text-gray-600">Belum ada artikel terbaru.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-xl shadow-lg">
                <div class="p-4 sm:p-6 border-b border-gray-100">
                    <p class="text-xs uppercase text-gray-500 tracking-wide">Ringkasan</p>
                    <h3 class="text-lg font-semibold text-gray-900">Distribusi Sumber</h3>
                </div>

                <div class="p-4 sm:p-6 space-y-3">
                    @forelse ($collections as $c)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-3 h-3 rounded-full {{ $c['color'] ?? 'bg-blue-500' }}"></span>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $c['title'] }}</p>
                                    <p class="text-sm text-gray-500">{{ $c['desc'] }}</p>
                                </div>
                            </div>
                            <span class="text-sm font-semibold text-gray-700">{{ $c['count'] }}x</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-600">Belum ada data sumber.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- MANAGE TABLE --}}
        <div id="manage" class="mx-auto px-6">
            <div class="bg-white border border-gray-100 rounded-xl shadow-lg overflow-hidden">
                <div
                    class="p-4 sm:p-6 border-b border-gray-100 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-xs uppercase text-gray-500 tracking-wide">Kelola Knowledge</p>
                        <h3 class="text-lg font-semibold text-gray-900">Publish & Aktivasi</h3>
                    </div>
                    <div class="flex flex-col gap-2 w-full lg:w-auto">
                        <form method="GET" action="{{ route('knowledge.index') }}"
                            class="flex flex-col sm:flex-row flex-wrap gap-2 w-full">
                            <input type="text" name="q" value="{{ request('q') }}"
                                class="w-full sm:w-64 px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 placeholder:text-gray-400 focus:ring-2 focus:ring-blue-200 focus:border-blue-300"
                                placeholder="Cari judul / isi / source..." />

                            <select name="published"
                                class="w-full sm:w-40 px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 focus:ring-2 focus:ring-blue-200 focus:border-blue-300">
                                <option value="">{{ __('Semua') }}</option>
                                <option value="1" @selected(request('published') === '1')>Published</option>
                                <option value="0" @selected(request('published') === '0')>Draft</option>
                            </select>

                            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                                <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm w-full sm:w-auto">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    Cari
                                </button>

                                @if (request('q') || request('published') !== null)
                                    <a href="{{ route('knowledge.index') }}"
                                        class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm w-full sm:w-auto">
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </form>

                        <div class="text-sm text-gray-500 text-right">
                            Total: <span class="font-semibold text-gray-900">{{ $entries->total() ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">Judul</th>
                                <th class="px-4 py-3 text-left font-semibold">Source</th>
                                <th class="px-4 py-3 text-left font-semibold">Publish</th>
                                <th class="px-4 py-3 text-left font-semibold">Updated</th>
                                <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse ($entries as $e)
                                <tr class="hover:bg-gray-50/60">
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-gray-900">{{ $e->title }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="px-2 py-1 rounded-full bg-gray-100 text-xs font-semibold text-gray-700 uppercase">
                                            {{ ucfirst($e->source ?? 'manual') }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-gray-600">
                                        <span @class([
                                            'inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold border',
                                            'bg-emerald-50 text-emerald-700 border-emerald-100' =>
                                                (bool) $e->is_published,
                                            'bg-gray-50 text-gray-700 border-gray-100' => !(bool) $e->is_published,
                                        ])>
                                            <span @class([
                                                'inline-block h-2 w-2 rounded-full',
                                                'bg-emerald-500' => (bool) $e->is_published,
                                                'bg-gray-400' => !(bool) $e->is_published,
                                            ])></span>
                                            {{ $e->is_published ? 'Published' : 'Draft' }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-gray-600">
                                        {{ optional($e->updated_at)->diffForHumans() ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('knowledge.edit', $e->id) }}"
                                                class="px-3 py-2 rounded-lg bg-white border border-gray-200 text-gray-700 text-xs font-semibold hover:bg-gray-50">
                                                Edit
                                            </a>

                                            <button type="button"
                                                class="px-3 py-2 rounded-lg text-xs font-semibold bg-blue-500 text-white hover:bg-blue-600 transition-all"
                                                data-publish data-action="{{ route('knowledge.toggleActive', $e->id) }}"
                                                data-title="@js($e->title)"
                                                data-mode="{{ $e->is_published ? 'unpublish' : 'publish' }}">
                                                {{ $e->is_published ? 'Unpublish' : 'Publish' }}
                                            </button>

                                            <button type="button"
                                                class="px-3 py-2 rounded-lg bg-white border border-rose-200 text-rose-600 text-xs font-semibold hover:bg-rose-50"
                                                data-delete data-action="{{ route('knowledge.destroy', $e->id) }}"
                                                data-title="@js($e->title)">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                        Belum ada data.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (isset($entries) && method_exists($entries, 'links'))
                    <div class="p-4 border-t border-gray-100">
                        {{ $entries->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- PUBLISH / UNPUBLISH MODAL --}}
    <div id="knowledge-publish-modal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-black/50" data-modal-close></div>

        <div class="relative mx-auto mt-24 w-[92%] max-w-lg">
            <div class="rounded-2xl bg-white shadow-xl border border-gray-100 overflow-hidden">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <div>
                        <p class="text-xs uppercase tracking-widest text-indigo-600 font-semibold">Konfirmasi</p>
                        <h2 class="text-lg font-semibold text-gray-900" id="publish-mode">Publish</h2>
                    </div>
                    <button type="button" class="text-gray-400 hover:text-gray-600" data-modal-close aria-label="Close">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <div class="px-6 py-4 space-y-3">
                    <p class="text-sm text-gray-700">
                        Yakin <span class="font-semibold text-gray-900" id="publish-mode-label">publish</span> knowledge
                        ini?
                    </p>
                    <div class="rounded-xl bg-blue-50 border border-blue-100 px-4 py-3">
                        <p class="text-sm font-semibold text-blue-800" id="publish-title">-</p>
                    </div>
                </div>

                <div class="border-t border-gray-100 px-6 py-4 flex justify-end gap-2">
                    <button type="button"
                        class="px-4 py-2 rounded-lg bg-white border border-gray-200 text-gray-700 text-sm font-semibold hover:bg-gray-50"
                        data-modal-close>
                        Batal
                    </button>

                    <form id="publish-form" method="POST" action="#">
                        @csrf
                        @method('PATCH')
                        <button
                            class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">
                            Lanjutkan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- DELETE MODAL --}}
    <div id="knowledge-delete-modal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-black/50" data-modal-close></div>

        <div class="relative mx-auto mt-24 w-[92%] max-w-lg">
            <div class="rounded-2xl bg-white shadow-xl border border-gray-100 overflow-hidden">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <div>
                        <p class="text-xs uppercase tracking-widest text-rose-600 font-semibold">Konfirmasi</p>
                        <h2 class="text-lg font-semibold text-gray-900">Hapus Knowledge</h2>
                    </div>
                    <button type="button" class="text-gray-400 hover:text-gray-600" data-modal-close aria-label="Close">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <div class="px-6 py-4 space-y-3">
                    <p class="text-sm text-gray-700">
                        Yakin menghapus knowledge ini secara permanen? Tindakan tidak bisa dibatalkan.
                    </p>
                    <div class="rounded-xl bg-rose-50 border border-rose-100 px-4 py-3">
                        <p class="text-sm font-semibold text-rose-800" id="delete-title">-</p>
                    </div>
                </div>

                <div class="border-t border-gray-100 px-6 py-4 flex justify-end gap-2">
                    <button type="button"
                        class="px-4 py-2 rounded-lg bg-white border border-gray-200 text-gray-700 text-sm font-semibold hover:bg-gray-50"
                        data-modal-close>
                        Batal
                    </button>

                    <form id="delete-form" method="POST" action="#">
                        @csrf
                        @method('DELETE')
                        <button
                            class="px-4 py-2 rounded-lg bg-rose-600 text-white text-sm font-semibold hover:bg-rose-700">
                            Ya, hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const publishModal = document.getElementById('knowledge-publish-modal');
            const deleteModal = document.getElementById('knowledge-delete-modal');
            const publishTitleEl = document.getElementById('publish-title');
            const publishModeEl = document.getElementById('publish-mode');
            const publishModeLabelEl = document.getElementById('publish-mode-label');
            const deleteTitleEl = document.getElementById('delete-title');
            const publishForm = document.getElementById('publish-form');
            const deleteForm = document.getElementById('delete-form');
            let activeModal = null;

            const lockScroll = (lock) => {
                document.documentElement.classList.toggle('overflow-hidden', lock);
                document.body.classList.toggle('overflow-hidden', lock);
            };

            const openModal = (modal) => {
                if (!modal) return;
                activeModal = modal;
                modal.classList.remove('hidden');
                modal.setAttribute('aria-hidden', 'false');
                lockScroll(true);
            };

            const closeActiveModal = () => {
                if (!activeModal) return;
                activeModal.classList.add('hidden');
                activeModal.setAttribute('aria-hidden', 'true');
                activeModal = null;
                lockScroll(false);
            };

            document.addEventListener('click', (event) => {
                if (event.target?.closest('[data-modal-close]')) {
                    closeActiveModal();
                    return;
                }

                const publishTrigger = event.target?.closest('[data-publish]');
                if (publishTrigger) {
                    const action = publishTrigger.getAttribute('data-action') || '#';
                    const title = publishTrigger.getAttribute('data-title') || '-';
                    const mode = publishTrigger.getAttribute('data-mode') || 'publish';
                    const modeText = mode === 'unpublish' ? 'Unpublish' : 'Publish';

                    if (publishForm) publishForm.setAttribute('action', action);
                    if (publishTitleEl) publishTitleEl.textContent = title;
                    if (publishModeEl) publishModeEl.textContent = modeText;
                    if (publishModeLabelEl) publishModeLabelEl.textContent = modeText.toLowerCase();
                    const methodInput = publishForm?.querySelector('input[name="_method"]');
                    if (methodInput) methodInput.value = 'PATCH';
                    openModal(publishModal);
                    return;
                }

                const deleteTrigger = event.target?.closest('[data-delete]');
                if (deleteTrigger) {
                    const action = deleteTrigger.getAttribute('data-action') || '#';
                    const title = deleteTrigger.getAttribute('data-title') || '-';
                    if (deleteForm) deleteForm.setAttribute('action', action);
                    if (deleteTitleEl) deleteTitleEl.textContent = title;
                    const methodInput = deleteForm?.querySelector('input[name="_method"]');
                    if (methodInput) methodInput.value = 'DELETE';
                    openModal(deleteModal);
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') closeActiveModal();
            });
        });
    </script>
@endsection
