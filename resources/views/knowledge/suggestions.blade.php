@extends('layouts.app')

@section('title', __('Knowledge Suggestions'))
@section('header', __('Knowledge Suggestions'))

@section('content')
    <div class="py-6 px-6 space-y-6">
        <div class="mx-auto">
            <div
                class="bg-white/80 backdrop-blur-lg border border-white/40 shadow-xl rounded-2xl p-6 sm:p-8 flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                <div class="space-y-2">
                    <p class="text-sm font-semibold text-indigo-600 uppercase tracking-widest">Suggestions</p>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Knowledge Suggestions</h1>
                    <p class="text-sm text-gray-600">Data masuk dari API (pending review).</p>
                </div>

                <div class="flex flex-col gap-3 w-full xl:w-auto">
                    <form method="GET" action="{{ route('knowledge.suggestions') }}"
                        class="flex flex-col sm:flex-row flex-wrap gap-2 w-full">
                        <input type="text" name="q" value="{{ request('q') }}"
                            class="w-full sm:w-64 px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 placeholder:text-gray-400 focus:ring-2 focus:ring-blue-200 focus:border-blue-300"
                            placeholder="Cari judul / isi / source..." />

                        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                            <button
                                class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm w-full sm:w-auto">
                                <i class="fa-solid fa-magnifying-glass"></i> Cari
                            </button>

                            <a href="{{ route('knowledge.index') }}"
                                class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm w-full sm:w-auto">
                                <i class="fa-solid fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </form>

                    @if (($suggestions->total() ?? 0) > 0)
                        <button type="button"
                            class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-rose-600 text-white rounded-lg text-sm font-semibold hover:bg-rose-700 shadow-sm w-full sm:w-auto"
                            data-reject-all data-action="{{ route('knowledge.suggestions.rejectAll') }}">
                            <i class="fa-solid fa-ban"></i>
                            Reject All
                        </button>
                    @endif
                </div>
            </div>
        </div>

        @if (session('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition.opacity
                class="bg-green-50 px-8 py-6 rounded-2xl shadow-lg border-l-4 border-green-500">
                <div class="flex items-center gap-5">
                    <div class="p-1 bg-green-200 rounded-full text-xs border border-green-800 text-green-800">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-green-900">Berhasil</span>
                        <span class="text-green-700">{{ session('success') }}</span>
                    </div>
                </div>
            </div>
        @endif

        <div class="mx-auto">
            <div class="bg-white border border-gray-100 rounded-2xl shadow-lg overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase text-gray-500 tracking-wide">Pending</p>
                        <h3 class="text-lg font-semibold text-gray-900">Approve / Reject</h3>
                    </div>
                    <div class="text-sm text-gray-500">
                        Total: <span class="font-semibold text-gray-900">{{ $suggestions->total() ?? 0 }}</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">Judul</th>
                                <th class="px-4 py-3 text-left font-semibold">Preview</th>
                                <th class="px-4 py-3 text-left font-semibold">Source</th>
                                <th class="px-4 py-3 text-left font-semibold">Created</th>
                                <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse($suggestions as $s)
                                <tr class="hover:bg-gray-50/60">
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-gray-900">{{ $s->title }}</div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="text-sm text-gray-700 line-clamp-2">
                                            {{ \Illuminate\Support\Str::limit(strip_tags($s->answer), 160) }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <span
                                            class="px-2 py-1 rounded-full bg-gray-100 text-xs font-semibold text-gray-700">
                                            {{ ucfirst($s->source ?? 'manual') }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-gray-600">
                                        {{ optional($s->created_at)->diffForHumans() ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="flex justify-end gap-2">
                                            <button type="button"
                                                class="px-3 py-2 rounded-lg bg-white border border-blue-200 text-blue-600 text-xs font-semibold hover:bg-blue-50"
                                                data-preview data-preview-id="{{ $s->id }}"
                                                data-title="{{ $s->title }}"
                                                data-source="{{ ucfirst($s->source ?? 'manual') }}"
                                                data-created="{{ optional($s->created_at)->diffForHumans() ?? '-' }}">
                                                Preview
                                            </button>

                                            <button type="button"
                                                class="px-3 py-2 rounded-lg bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700"
                                                data-approve
                                                data-action="{{ route('knowledge.suggestions.approve', $s->id) }}"
                                                data-title="{{ $s->title }}" data-answer="{{ $s->answer }}"
                                                data-source="{{ $s->source ?? 'manual' }}">
                                                Approve
                                            </button>

                                            <button type="button"
                                                class="px-3 py-2 rounded-lg bg-white border border-rose-200 text-rose-600 text-xs font-semibold hover:bg-rose-50"
                                                data-reject
                                                data-action="{{ route('knowledge.suggestions.reject', $s->id) }}"
                                                data-title="@js($s->title)">
                                                Reject
                                            </button>
                                        </div>

                                        {{-- HTML hasil markdown (server-side) buat modal preview --}}
                                        <template id="preview-html-{{ $s->id }}">
                                            {!! \Illuminate\Support\Str::markdown($s->answer) !!}
                                        </template>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-10 text-center text-gray-500">
                                        Belum ada suggestion.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-100">
                    {{ $suggestions->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== MODALS ===================== --}}

    {{-- PREVIEW MODAL --}}
    <div id="suggestion-preview-modal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-black/50" data-modal-close></div>

        <div class="relative mx-auto mt-10 w-[92%] max-w-4xl">
            <div class="rounded-2xl bg-white shadow-xl border border-gray-100 overflow-hidden">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <div>
                        <p class="text-xs uppercase tracking-widest text-indigo-600 font-semibold" id="preview-source"></p>
                        <h2 class="text-lg font-semibold text-gray-900" id="preview-title">Preview</h2>
                        <p class="text-xs text-gray-500 mt-1" id="preview-created"></p>
                    </div>

                    <button type="button" class="text-gray-400 hover:text-gray-600" data-modal-close aria-label="Close">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <div class="max-h-[70vh] overflow-y-auto px-6 py-4">
                    <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                        {{-- prose biar mirip artikel --}}
                        <div id="preview-content" class="prose max-w-none prose-sm text-gray-800"></div>
                    </div>
                </div>

                <div class="border-t border-gray-100 px-6 py-4 flex justify-end gap-2">
                    <button type="button"
                        class="px-4 py-2 rounded-lg bg-white border border-gray-200 text-gray-700 text-sm font-semibold hover:bg-gray-50"
                        data-modal-close>
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- APPROVE + EDIT MODAL --}}
    <div id="suggestion-approve-modal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-black/50" data-modal-close></div>

        <div class="relative mx-auto mt-10 w-[96%] max-w-4xl">
            <div class="rounded-2xl bg-white shadow-2xl border border-gray-100 overflow-hidden">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <div>
                        <p class="text-xs uppercase tracking-widest text-emerald-600 font-semibold">Edit & Approve</p>
                        <h2 class="text-lg font-semibold text-gray-900">Periksa & Publikasikan</h2>
                    </div>
                    <button type="button" class="text-gray-400 hover:text-gray-600" data-modal-close aria-label="Close">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form id="approve-form" method="POST" action="#" class="space-y-4">
                    @csrf
                    <div class="px-6 py-4 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label class="text-sm font-semibold text-gray-800" for="approve-title-input">Judul</label>
                                <input id="approve-title-input" name="title" type="text"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-300 text-sm"
                                    placeholder="Judul knowledge" />
                            </div>
                            <div class="space-y-1">
                                <label class="text-sm font-semibold text-gray-800"
                                    for="approve-source-input">Sumber</label>
                                <input id="approve-source-input" name="source" type="text"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-300 text-sm"
                                    placeholder="manual / api / import" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-semibold text-gray-800">Jawaban (Markdown)</label>
                                <p class="text-xs text-gray-500">Konten akan disimpan sebagai markdown.</p>
                            </div>

                            <div data-tui-editor data-editor-target="answer" id="approve-editor"
                                class="border border-gray-200 rounded-lg overflow-hidden"></div>
                            <textarea id="approve-answer" name="answer" class="hidden"></textarea>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 px-6 py-4 flex justify-end gap-2">
                        <button type="button"
                            class="px-4 py-2 rounded-lg bg-white border border-gray-200 text-gray-700 text-sm font-semibold hover:bg-gray-50"
                            data-modal-close>
                            Batal
                        </button>

                        <button type="submit"
                            class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">
                            Approve & Publish
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- REJECT MODAL --}}
    <div id="suggestion-reject-modal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-black/50" data-modal-close></div>

        <div class="relative mx-auto mt-24 w-[92%] max-w-lg">
            <div class="rounded-2xl bg-white shadow-xl border border-gray-100 overflow-hidden">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <div>
                        <p class="text-xs uppercase tracking-widest text-rose-600 font-semibold">Konfirmasi</p>
                        <h2 class="text-lg font-semibold text-gray-900">Reject Suggestion</h2>
                    </div>
                    <button type="button" class="text-gray-400 hover:text-gray-600" data-modal-close aria-label="Close">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <div class="px-6 py-4">
                    <p class="text-sm text-gray-700">
                        Yakin reject suggestion ini? (Suggestion akan ditandai sudah diproses)
                    </p>
                    <div class="mt-3 rounded-xl bg-rose-50 border border-rose-100 px-4 py-3">
                        <p class="text-sm font-semibold text-rose-800" id="reject-title">-</p>
                    </div>
                </div>

                <div class="border-t border-gray-100 px-6 py-4 flex justify-end gap-2">
                    <button type="button"
                        class="px-4 py-2 rounded-lg bg-white border border-gray-200 text-gray-700 text-sm font-semibold hover:bg-gray-50"
                        data-modal-close>
                        Batal
                    </button>

                    <form id="reject-form" method="POST" action="#">
                        @csrf
                        <button
                            class="px-4 py-2 rounded-lg bg-rose-600 text-white text-sm font-semibold hover:bg-rose-700">
                            Reject
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- REJECT ALL MODAL --}}
    <div id="suggestion-reject-all-modal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-black/50" data-modal-close></div>

        <div class="relative mx-auto mt-24 w-[92%] max-w-lg">
            <div class="rounded-2xl bg-white shadow-xl border border-gray-100 overflow-hidden">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <div>
                        <p class="text-xs uppercase tracking-widest text-rose-600 font-semibold">Konfirmasi</p>
                        <h2 class="text-lg font-semibold text-gray-900">Reject Semua Suggestions</h2>
                    </div>
                    <button type="button" class="text-gray-400 hover:text-gray-600" data-modal-close aria-label="Close">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <div class="px-6 py-4 space-y-3">
                    <p class="text-sm text-gray-700">
                        Yakin menolak <strong>semua</strong> suggestion pending? Data akan dihapus.
                    </p>
                    <div class="rounded-xl bg-rose-50 border border-rose-100 px-4 py-3">
                        <p class="text-sm font-semibold text-rose-800">Tindakan tidak bisa dibatalkan.</p>
                    </div>
                </div>

                <div class="border-t border-gray-100 px-6 py-4 flex justify-end gap-2">
                    <button type="button"
                        class="px-4 py-2 rounded-lg bg-white border border-gray-200 text-gray-700 text-sm font-semibold hover:bg-gray-50"
                        data-modal-close>
                        Batal
                    </button>

                    <form id="reject-all-form" method="POST" action="#">
                        @csrf
                        <button
                            class="px-4 py-2 rounded-lg bg-rose-600 text-white text-sm font-semibold hover:bg-rose-700">
                            Ya, reject semua
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // ===== Elements
        const previewModal = document.getElementById('suggestion-preview-modal');
        const approveModal = document.getElementById('suggestion-approve-modal');
        const rejectModal = document.getElementById('suggestion-reject-modal');
        const rejectAllModal = document.getElementById('suggestion-reject-all-modal');

        const titleEl = document.getElementById('preview-title');
        const sourceEl = document.getElementById('preview-source');
        const createdEl = document.getElementById('preview-created');
        const contentEl = document.getElementById('preview-content');

        const approveTitleInput = document.getElementById('approve-title-input');
        const approveSourceInput = document.getElementById('approve-source-input');
        const approveAnswerTextarea = document.getElementById('approve-answer');
        const approveEditorContainer = document.getElementById('approve-editor');
        const rejectTitleEl = document.getElementById('reject-title');
        const approveForm = document.getElementById('approve-form');
        const rejectForm = document.getElementById('reject-form');
        const rejectAllForm = document.getElementById('reject-all-form');

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

        // sanitize ringan: buang tag berbahaya + event handlers + javascript: links
        const sanitizeHtml = (html) => {
            const wrapper = document.createElement('div');
            wrapper.innerHTML = html;

            wrapper.querySelectorAll('script, style, iframe, object, embed, link, meta').forEach(el => el.remove());

            wrapper.querySelectorAll('*').forEach((el) => {
                [...el.attributes].forEach(attr => {
                    const name = (attr.name || '').toLowerCase();
                    const value = (attr.value || '').toLowerCase();

                    if (name.startsWith('on')) el.removeAttribute(attr.name);
                    if ((name === 'href' || name === 'src') && value.startsWith('javascript:')) {
                        el.removeAttribute(attr.name);
                    }
                });
            });

            wrapper.querySelectorAll('a').forEach(a => {
                a.setAttribute('rel', 'noopener noreferrer');
                a.setAttribute('target', '_blank');
            });

            return wrapper.innerHTML;
        };

        // styling tambahan untuk HTML hasil markdown (biar konsisten)
        const styleHtml = (html) => {
            const template = document.createElement('template');
            template.innerHTML = html;

            template.content.querySelectorAll('blockquote').forEach((el) => {
                el.classList.add('border-l-4', 'border-blue-300', 'px-3', 'py-2', 'italic', 'text-zinc-700',
                    'bg-zinc-50', 'rounded', 'my-3');
            });

            template.content.querySelectorAll('pre').forEach((pre) => {
                pre.classList.add('bg-zinc-900', 'text-white', 'rounded-xl', 'p-3', 'overflow-x-auto', 'my-3',
                    'text-xs');
            });

            template.content.querySelectorAll('code').forEach((code) => {
                if (code.closest('pre')) return;
                code.classList.add('rounded', 'bg-zinc-100', 'px-1', 'py-px', 'text-[0.75rem]', 'font-mono');
            });

            template.content.querySelectorAll('table').forEach((table) => {
                table.classList.add('w-full', 'border-collapse', 'my-3');
                const wrap = document.createElement('div');
                wrap.className = 'w-full overflow-x-auto rounded-lg border border-gray-100 bg-white';
                table.parentNode?.insertBefore(wrap, table);
                wrap.appendChild(table);
            });

            template.content.querySelectorAll('th').forEach((th) => th.classList.add('bg-gray-50', 'border',
                'border-gray-200', 'px-2', 'py-1', 'text-left', 'font-semibold'));
            template.content.querySelectorAll('td').forEach((td) => td.classList.add('border', 'border-gray-200',
                'px-2', 'py-1', 'align-top'));

            return template.innerHTML;
        };

        document.addEventListener('click', (event) => {
            // close modal
            if (event.target?.closest('[data-modal-close]')) {
                closeActiveModal();
                return;
            }

            // preview trigger
            const previewTrigger = event.target?.closest('[data-preview]');
            if (previewTrigger) {
                const id = previewTrigger.getAttribute('data-preview-id');
                const title = previewTrigger.getAttribute('data-title') || '';
                const source = previewTrigger.getAttribute('data-source') || '';
                const created = previewTrigger.getAttribute('data-created') || '';

                const tpl = document.getElementById(`preview-html-${id}`);
                const rawHtml = tpl ? tpl.innerHTML : '<p>-</p>';

                if (titleEl) titleEl.textContent = title;
                if (sourceEl) sourceEl.textContent = source;
                if (createdEl) createdEl.textContent = created;

                if (contentEl) {
                    const safe = sanitizeHtml(rawHtml);
                    contentEl.innerHTML = styleHtml(safe);
                }

                openModal(previewModal);
                return;
            }

            // approve trigger
            const approveTrigger = event.target?.closest('[data-approve]');
            if (approveTrigger) {
                const action = approveTrigger.getAttribute('data-action') || '#';
                const title = approveTrigger.dataset.title || '';
                const source = approveTrigger.dataset.source || 'manual';
                const answer = approveTrigger.dataset.answer || '';

                if (approveForm) approveForm.setAttribute('action', action);
                if (approveTitleInput) approveTitleInput.value = title;
                if (approveSourceInput) approveSourceInput.value = source;
                if (approveAnswerTextarea) approveAnswerTextarea.value = answer;

                if (approveEditorContainer) {
                    approveEditorContainer.dispatchEvent(new CustomEvent('tui-editor:set', {
                        detail: answer
                    }));
                }

                openModal(approveModal);
                return;
            }

            // reject trigger
            const rejectTrigger = event.target?.closest('[data-reject]');
            if (rejectTrigger) {
                const action = rejectTrigger.getAttribute('data-action') || '#';
                const title = rejectTrigger.getAttribute('data-title') || '-';
                if (rejectForm) rejectForm.setAttribute('action', action);
                if (rejectTitleEl) rejectTitleEl.textContent = title;
                openModal(rejectModal);
                return;
            }

            const rejectAllTrigger = event.target?.closest('[data-reject-all]');
            if (rejectAllTrigger) {
                const action = rejectAllTrigger.getAttribute('data-action') || '#';
                if (rejectAllForm) rejectAllForm.setAttribute('action', action);
                openModal(rejectAllModal);
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') closeActiveModal();
        });
    </script>
@endsection
