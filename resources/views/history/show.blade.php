@extends('layouts.app')

@section('title', __('History Detail'))

@section('header', __('History Detail'))

@section('content')
    <div class="p-6 space-y-6" id="history-detail-root"
        data-history-mode="{{ in_array(strtolower((string) auth()->user()?->role), ['admin', 'superadmin'], true) ? 'admin' : 'user' }}"
        data-admin-endpoint="{{ route('admin.history.data') }}"
        data-history-session-id="{{ $sessionId }}">
        <div
            class="bg-white/80 backdrop-blur-lg border border-white/40 shadow-xl rounded-2xl p-6 sm:p-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-2">
                <p class="text-sm font-semibold text-indigo-600 uppercase tracking-widest">History</p>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Detail History</h1>
                <p class="text-sm text-gray-600">Isi percakapan dalam sesi ini.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                <a href="{{ route('history.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm w-full sm:w-auto">
                    <i class="fa-solid fa-arrow-left-long"></i>
                    Kembali
                </a>
                <span
                    class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 w-full sm:w-auto">
                    <i class="fa-solid fa-hashtag text-blue-600"></i>
                    <span id="history-detail-session">Session</span>
                </span>
                <span
                    class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 w-full sm:w-auto">
                    <i class="fa-solid fa-message text-emerald-600"></i>
                    <span id="history-detail-count">0 pesan</span>
                </span>
                <button type="button"
                    class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm w-full sm:w-auto"
                    id="history-copy-session">
                    <i class="fa-regular fa-copy"></i>
                    Salin Session ID
                </button>
            </div>
        </div>

        <div class="mx-auto bg-white border border-gray-100 rounded-xl shadow-sm">
            <div id="history-detail-loading" class="p-6 text-sm text-gray-500">Memuat chat...</div>
            <div id="history-detail-error" class="hidden p-6 text-sm text-red-600"></div>
            <div id="history-detail-empty" class="hidden p-6 text-sm text-gray-500">Tidak ada pesan untuk sesi ini.</div>
            <div id="history-detail-list" class="space-y-4 p-6"></div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dompurify@3.1.6/dist/purify.min.js"></script>
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
        import {
            getAuth,
            onAuthStateChanged,
            signInAnonymously,
            signInWithCustomToken
        } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-auth.js";
        import {
            getFirestore,
            collection,
            getDocs
        } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-firestore.js";

        const firebaseConfig = @json(config('services.firebase', []));
        const rootEl = document.getElementById('history-detail-root');
        const historyMode = rootEl?.dataset?.historyMode || 'user';
        const adminEndpoint = rootEl?.dataset?.adminEndpoint || '';
        const sessionId = rootEl?.dataset?.historySessionId || '';
        const loadingEl = document.getElementById('history-detail-loading');
        const errorEl = document.getElementById('history-detail-error');
        const emptyEl = document.getElementById('history-detail-empty');
        const listEl = document.getElementById('history-detail-list');
        const sessionLabelEl = document.getElementById('history-detail-session');
        const countEl = document.getElementById('history-detail-count');
        const copyButton = document.getElementById('history-copy-session');
        const firebaseCustomToken = document
            .querySelector('meta[name="firebase-custom-token"]')
            ?.getAttribute('content');

        const setState = ({ loading, empty, error }) => {
            if (loadingEl) loadingEl.classList.toggle('hidden', !loading);
            if (emptyEl) emptyEl.classList.toggle('hidden', !empty);
            if (errorEl) errorEl.classList.toggle('hidden', !error);
        };

        const escapeHtml = (value) => {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;',
            };
            return String(value ?? '').replace(/[&<>"']/g, (m) => map[m]);
        };

        const normalizeDate = (value) => {
            if (!value) return null;
            if (value instanceof Date) return value;
            if (typeof value === 'string' || typeof value === 'number') {
                const parsed = new Date(value);
                return Number.isNaN(parsed.getTime()) ? null : parsed;
            }
            if (typeof value === 'object') {
                if (typeof value.toDate === 'function') return value.toDate();
                if (typeof value.seconds === 'number') return new Date(value.seconds * 1000);
            }
            return null;
        };

        const formatRelative = (date) => {
            if (!date) return '-';
            const diffMs = Date.now() - date.getTime();
            const diffSec = Math.floor(diffMs / 1000);
            const diffMin = Math.floor(diffSec / 60);
            const diffHour = Math.floor(diffMin / 60);
            const diffDay = Math.floor(diffHour / 24);

            if (diffMin < 1) return 'baru saja';
            if (diffMin < 60) return `${diffMin} menit lalu`;
            if (diffHour < 24) return `${diffHour} jam lalu`;
            if (diffDay < 7) return `${diffDay} hari lalu`;
            return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        };

        const styleMarkdown = (html) => {
            const template = document.createElement('template');
            template.innerHTML = html;

            template.content.querySelectorAll('blockquote').forEach((el) => {
                el.classList.add(
                    'border-l-4',
                    'border-blue-300',
                    'px-2',
                    'pe-10',
                    'py-2',
                    'italic',
                    'text-[0.95rem]',
                    'text-zinc-700',
                    'mt-2',
                    'bg-zinc-100',
                    'rounded',
                    'mb-4'
                );
            });
            template.content.querySelectorAll('p').forEach((el) => {
                el.classList.add('leading-relaxed');
            });
            template.content.querySelectorAll('h1').forEach((el) => {
                el.classList.add('mb-2', 'text-lg', 'font-semibold', 'text-zinc-900');
            });
            template.content.querySelectorAll('h2').forEach((el) => {
                el.classList.add('mb-2', 'text-base', 'font-semibold', 'text-zinc-900');
            });
            template.content.querySelectorAll('h3').forEach((el) => {
                el.classList.add('mb-2', 'text-sm', 'font-semibold', 'text-zinc-900');
            });
            template.content.querySelectorAll('strong').forEach((el) => {
                el.classList.add('font-semibold');
            });
            template.content.querySelectorAll('hr').forEach((el) => {
                el.classList.add('my-5', 'border-0', 'h-px', 'bg-gray-300');
            });
            template.content.querySelectorAll('ul').forEach((el) => {
                el.classList.add('mb-2', 'ml-4', 'list-disc', 'space-y-1');
            });
            template.content.querySelectorAll('ol').forEach((el) => {
                el.classList.add('mb-2', 'pl-4', 'list-decimal', 'space-y-1');
            });
            template.content.querySelectorAll('li').forEach((el) => {
                el.classList.add('leading-relaxed');
            });

            template.content.querySelectorAll('pre').forEach((pre) => {
                const code = pre.querySelector('code');
                const codeText = code?.textContent || '';
                const codeClass = code?.className || '';
                const wrapper = document.createElement('div');
                wrapper.className = 'bg-zinc-800 text-white ml-7 rounded-xl overflow-hidden my-3';
                wrapper.innerHTML = `
                    <div class="flex text-xs py-1 px-3 items-center justify-between bg-zinc-950 font-mono">
                        <span>Code</span>
                        <button
                            type="button"
                            class="markdown-copy-button cursor-pointer hover:text-zinc-300 transition-all flex items-center gap-1"
                            aria-label="Salin Kode"
                            title="Salin Kode">
                            <i class="fa-regular fa-clone"></i>
                            <span>Salin Kode</span>
                        </button>
                    </div>
                    <pre class="max-w-full overflow-x-auto text-[0.75rem] font-mono">
                        <code class="${codeClass}">${escapeHtml(codeText)}</code>
                    </pre>
                `;
                pre.replaceWith(wrapper);
            });

            template.content.querySelectorAll('code').forEach((el) => {
                if (el.closest('pre')) return;
                el.classList.add('rounded', 'bg-zinc-100', 'px-1', 'py-px', 'text-[0.75rem]', 'font-mono');
            });

            template.content.querySelectorAll('table').forEach((table) => {
                table.classList.add('w-full', 'border-collapse');
                const wrapper = document.createElement('div');
                wrapper.className = 'bg-zinc-50 p-2 rounded w-full overflow-x-auto';
                table.parentNode?.insertBefore(wrapper, table);
                wrapper.appendChild(table);
            });
            template.content.querySelectorAll('thead').forEach((el) => {
                el.classList.add('bg-zinc-100');
            });
            template.content.querySelectorAll('tr').forEach((el) => {
                el.classList.add('border-b', 'border-blue-200', 'last:border-0');
            });
            template.content.querySelectorAll('th').forEach((el) => {
                el.classList.add(
                    'border',
                    'border-blue-200',
                    'px-2',
                    'py-1',
                    'text-left',
                    'font-semibold',
                    'text-base',
                    'bg-blue-100'
                );
            });
            template.content.querySelectorAll('td').forEach((el) => {
                el.classList.add('border', 'border-blue-200', 'px-2', 'py-1', 'align-top');
            });

            return template.innerHTML;
        };

        const renderMessage = (message) => {
            const wrapper = document.createElement('div');
            const roleValue = message.role ? message.role.toLowerCase() : 'unknown';
            const roleLabel = message.role ? message.role.toUpperCase() : 'UNKNOWN';
            const timeLabel = message.time ? message.time : '-';
            const rawText = typeof message.text === 'string' ? message.text : '';
            const html = window.marked ? window.marked.parse(rawText) : escapeHtml(rawText);
            const safeHtml = window.DOMPurify
                ? window.DOMPurify.sanitize(html, { ADD_ATTR: ['style'] })
                : html;
            const styledHtml = styleMarkdown(safeHtml);
            const isUser = roleValue === 'user';
            const roleBadge = isUser
                ? 'bg-slate-100 text-slate-600'
                : 'bg-indigo-100 text-indigo-700';
            const bubble = isUser ? 'bg-white border border-slate-200' : 'bg-indigo-50 border border-indigo-100';
            const align = isUser ? 'items-end text-right' : 'items-start text-left';

            wrapper.className = `flex flex-col ${align}`;
            wrapper.innerHTML = `
                <div class="flex items-center gap-2 text-xs text-gray-500 mb-2">
                    <span class="px-2 py-1 rounded-full text-[10px] font-semibold ${roleBadge}">
                        ${escapeHtml(roleLabel)}
                    </span>
                    <span>${escapeHtml(timeLabel)}</span>
                </div>
                <div class="w-full rounded-xl px-4 py-3 ${bubble}">
                    <div class="prose max-w-none prose-sm text-gray-800">${styledHtml}</div>
                </div>
            `;
            return wrapper;
        };

        const loadAdminMessages = async (sessionIdValue) => {
            if (!adminEndpoint || !sessionIdValue) {
                return [];
            }
            const url = new URL(adminEndpoint, window.location.origin);
            url.searchParams.set('sessionId', sessionIdValue);
            const response = await fetch(url.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            if (!response.ok) {
                throw new Error('Admin history request failed');
            }
            const payload = await response.json();
            return Array.isArray(payload?.items) ? payload.items : [];
        };

        const loadUserMessages = async (uid) => {
            const db = getFirestore();
            const sessionsRef = collection(db, 'sessions', uid, 'messages');
            const snapshot = await getDocs(sessionsRef);
            const items = [];

            snapshot.forEach((docSnap) => {
                const data = docSnap.data() || {};
                const dateValue = normalizeDate(data.updatedAt || data.updated_at || data.createdAt || data.created_at || data.time);
                items.push({
                    id: docSnap.id,
                    text: data.text || '',
                    role: data.role || '',
                    time: formatRelative(dateValue),
                    _sort: dateValue ? dateValue.getTime() : 0,
                });
            });

            items.sort((a, b) => b._sort - a._sort);
            return items;
        };

        const initDetail = async () => {
            setState({ loading: true, empty: false, error: false });

            if (sessionLabelEl) {
                sessionLabelEl.textContent = sessionId || 'Session';
            }
            if (copyButton) {
                copyButton.addEventListener('click', async () => {
                    if (!sessionId) return;
                    try {
                        await navigator.clipboard.writeText(sessionId);
                        copyButton.textContent = 'Tersalin';
                        setTimeout(() => {
                            copyButton.innerHTML = '<i class="fa-regular fa-copy"></i> Salin Session ID';
                        }, 1200);
                    } catch (err) {
                        copyButton.textContent = 'Gagal salin';
                    }
                });
            }

            if (listEl) {
                listEl.addEventListener('click', async (event) => {
                    const target = event.target?.closest('.markdown-copy-button');
                    if (!target) return;
                    const codeEl = target.closest('div')?.nextElementSibling?.querySelector('code');
                    const codeText = codeEl?.textContent || '';
                    if (!codeText) return;
                    try {
                        await navigator.clipboard.writeText(codeText);
                        target.textContent = 'Tersalin';
                        setTimeout(() => {
                            target.innerHTML = '<i class="fa-regular fa-clone"></i> <span>Salin Kode</span>';
                        }, 1200);
                    } catch (err) {
                        target.textContent = 'Gagal salin';
                    }
                });
            }

            if (historyMode === 'admin') {
                try {
                    const messages = await loadAdminMessages(sessionId);
                    if (!messages.length) {
                        setState({ loading: false, empty: true, error: false });
                        return;
                    }
                    const normalized = messages.map((data) => {
                        const dateValue = normalizeDate(data.createdAt || data.time || data.updatedAt);
                        return {
                            text: data.text || '',
                            role: data.role || '',
                            time: formatRelative(dateValue),
                            _sort: dateValue ? dateValue.getTime() : 0,
                        };
                    });
                    normalized.sort((a, b) => b._sort - a._sort);
                    if (countEl) {
                        countEl.textContent = `${normalized.length} pesan`;
                    }
                    if (listEl) {
                        listEl.innerHTML = '';
                        normalized.forEach((message) => listEl.appendChild(renderMessage(message)));
                    }
                    setState({ loading: false, empty: false, error: false });
                } catch (err) {
                    if (errorEl) errorEl.textContent = 'Gagal memuat detail chat.';
                    setState({ loading: false, empty: false, error: true });
                }
                return;
            }

            if (!firebaseConfig?.apiKey) {
                if (errorEl) errorEl.textContent = 'Konfigurasi Firebase belum diisi.';
                setState({ loading: false, empty: false, error: true });
                return;
            }

            const app = initializeApp(firebaseConfig);
            const auth = getAuth(app);

            const ensureAuth = async () => {
                if (auth.currentUser) return auth.currentUser;
                if (firebaseCustomToken) {
                    const result = await signInWithCustomToken(auth, firebaseCustomToken);
                    return result.user;
                }
                const result = await signInAnonymously(auth);
                return result.user;
            };

            try {
                await ensureAuth();
                onAuthStateChanged(auth, async (user) => {
                    if (!user) {
                        setState({ loading: false, empty: true, error: false });
                        return;
                    }
                    try {
                        const messages = await loadUserMessages(user.uid);
                        if (!messages.length) {
                            setState({ loading: false, empty: true, error: false });
                            return;
                        }
                        if (countEl) {
                            countEl.textContent = `${messages.length} pesan`;
                        }
                        if (listEl) {
                            listEl.innerHTML = '';
                            messages.forEach((message) => listEl.appendChild(renderMessage(message)));
                        }
                        setState({ loading: false, empty: false, error: false });
                    } catch (err) {
                        if (errorEl) errorEl.textContent = 'Gagal memuat detail chat.';
                        setState({ loading: false, empty: false, error: true });
                    }
                });
            } catch (err) {
                if (errorEl) errorEl.textContent = 'Autentikasi Firebase gagal.';
                setState({ loading: false, empty: false, error: true });
            }
        };

        initDetail();
    </script>
@endsection
