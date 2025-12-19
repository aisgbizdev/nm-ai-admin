@extends('layouts.app')

@section('title', __('History'))

@section('header', __('History'))

@section('content')
    <div class="p-6 space-y-6" id="history-root" data-history-collection="messages"
        data-history-mode="{{ in_array(strtolower((string) auth()->user()?->role), ['admin', 'superadmin'], true) ? 'admin' : 'user' }}"
        data-admin-endpoint="{{ route('admin.history.data') }}" data-history-detail-base="{{ url('/history') }}">
        <div
            class="bg-white/80 backdrop-blur-lg border border-white/40 shadow-xl rounded-2xl p-6 sm:p-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-2">
                <p class="text-sm font-semibold text-indigo-600 uppercase tracking-widest">History</p>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">History Chat</h1>
                <p class="text-sm text-gray-600">Riwayat percakapan dari Firebase.</p>
            </div>
            <div class="flex flex-wrap gap-2 w-full lg:w-auto">
                <button
                    class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm w-full sm:w-auto">
                    <i class="fa-solid fa-arrow-down-short-wide"></i>
                    Urutkan
                </button>
                <button
                    class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-indigo-600 text-white rounded-xl text-sm font-semibold hover:bg-indigo-700 shadow-sm w-full sm:w-auto">
                    <i class="fa-solid fa-file-arrow-down"></i>
                    Ekspor CSV
                </button>
            </div>
        </div>

        <div class="mx-auto px-6 bg-white border border-gray-100 rounded-xl shadow-lg">
            <div id="history-loading" class="p-6 text-sm text-gray-500">Memuat data...</div>
            <div id="history-error" class="hidden p-6 text-sm text-red-600"></div>
            <div id="history-empty" class="hidden p-6 text-sm text-gray-500">Belum ada riwayat chat.</div>
            <div id="history-list" class="divide-y divide-gray-100"></div>
        </div>
        <div id="history-pagination"
            class="hidden items-center justify-between rounded-xl bg-white/80 border border-white/40 shadow-lg px-4 py-3">
            <p class="text-xs text-gray-500" id="history-pagination-info">Menampilkan 0 dari 0</p>
            <div class="flex items-center gap-2" id="history-pagination-controls"></div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="module">
        import {
            initializeApp
        } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
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
        const rootEl = document.getElementById('history-root');
        const collectionName = rootEl?.dataset?.historyCollection || 'messages';
        const historyMode = rootEl?.dataset?.historyMode || 'user';
        const adminEndpoint = rootEl?.dataset?.adminEndpoint || '';
        const detailBase = rootEl?.dataset?.historyDetailBase || '';
        const loadingEl = document.getElementById('history-loading');
        const errorEl = document.getElementById('history-error');
        const emptyEl = document.getElementById('history-empty');
        const listEl = document.getElementById('history-list');
        const paginationEl = document.getElementById('history-pagination');
        const paginationInfoEl = document.getElementById('history-pagination-info');
        const paginationControlsEl = document.getElementById('history-pagination-controls');
        const firebaseCustomToken = document
            .querySelector('meta[name="firebase-custom-token"]')
            ?.getAttribute('content');
        const pageSize = 10;
        let currentPage = 1;
        let cachedItems = [];

        const setState = ({
            loading,
            empty,
            error
        }) => {
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
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        };

        const renderItem = (item, index) => {
            const statusClass =
                item.status?.toLowerCase() === 'selesai' ?
                'bg-emerald-50 text-emerald-700' :
                'bg-amber-50 text-amber-700';
            const detailHref = detailBase ? `${detailBase}/${encodeURIComponent(item.id)}` : '#';

            const wrapper = document.createElement('div');
            wrapper.className = 'p-4 sm:p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3';
            wrapper.innerHTML = `
                <div class="py-5 px-4 bg-blue-100 rounded border border-blue-300 text-blue-700 font-semibold text-sm">
                    ${index}
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-blue-700">
                        ${escapeHtml(item.user)}
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">${escapeHtml(item.time)}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 text-xs font-semibold rounded-full ${statusClass}">
                        ${escapeHtml(item.status)}
                    </span>
                    <a
                        href="${escapeHtml(detailHref)}"
                        class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-blue-50">
                        <i class="fa-regular fa-message"></i>
                        Lihat Chat
                    </a>
                </div>
            `;
            return wrapper;
        };

        const renderPagination = () => {
            if (!paginationEl || !paginationControlsEl || !paginationInfoEl) return;
            if (cachedItems.length <= pageSize) {
                paginationEl.classList.add('hidden');
                return;
            }

            paginationEl.classList.remove('hidden');
            const totalPages = Math.ceil(cachedItems.length / pageSize);
            const end = Math.min(currentPage * pageSize, cachedItems.length);
            paginationInfoEl.textContent = `Menampilkan ${end} dari ${cachedItems.length}`;
            paginationControlsEl.innerHTML = '';

            const makeButton = (label, page, disabled = false) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.textContent = label;
                btn.className = `px-3 py-1 rounded-lg text-xs font-semibold border ${
                    disabled ? 'text-gray-300 border-gray-200 cursor-not-allowed' : 'text-gray-700 border-gray-200 hover:bg-gray-50'
                }`;
                btn.disabled = disabled;
                btn.dataset.page = String(page);
                return btn;
            };

            paginationControlsEl.appendChild(makeButton('Prev', currentPage - 1, currentPage === 1));

            for (let page = 1; page <= totalPages; page += 1) {
                const btn = makeButton(String(page), page, false);
                if (page === currentPage) {
                    btn.className =
                        'px-3 py-1 rounded-lg text-xs font-semibold border text-white bg-indigo-600 border-indigo-600';
                }
                paginationControlsEl.appendChild(btn);
            }

            paginationControlsEl.appendChild(
                makeButton('Next', currentPage + 1, currentPage === totalPages)
            );
        };

        const renderPage = () => {
            if (!listEl) return;
            const totalPages = Math.ceil(cachedItems.length / pageSize) || 1;
            if (currentPage > totalPages) currentPage = totalPages;

            const startIndex = (currentPage - 1) * pageSize;
            const pageItems = cachedItems.slice(startIndex, startIndex + pageSize);

            listEl.innerHTML = '';
            pageItems.forEach((item, idx) => {
                listEl.appendChild(renderItem(item, startIndex + idx + 1));
            });
            renderPagination();
        };

        const loadHistory = async (uid) => {
            const db = getFirestore();
            const sessionsRef = collection(db, 'sessions', uid, collectionName);
            const snapshot = await getDocs(sessionsRef);
            const items = [];

            snapshot.forEach((docSnap) => {
                const data = docSnap.data() || {};
                const dateValue = normalizeDate(
                    data.updatedAt ||
                    data.updated_at ||
                    data.createdAt ||
                    data.created_at ||
                    data.time
                );
                const textValue = typeof data.text === 'string' ? data.text.trim() : '';
                const topicFromText = textValue ?
                    `${textValue.slice(0, 64)}${textValue.length > 64 ? '...' : ''}` : '';
                const summaryFromText = textValue ?
                    `${textValue.slice(0, 160)}${textValue.length > 160 ? '...' : ''}` : '';
                items.push({
                    id: docSnap.id,
                    topic: data.topic || data.title || data.subject || topicFromText ||
                        'Topik belum tersedia',
                    summary: data.summary || data.lastMessage || data.preview || summaryFromText ||
                        '',
                    user: data.user || data.userName || data.sender || data.role || 'User',
                    status: data.status || 'Selesai',
                    time: formatRelative(dateValue),
                    _sort: dateValue ? dateValue.getTime() : 0,
                });
            });

            if (!items.length) {
                return [];
            }

            items.sort((a, b) => b._sort - a._sort);
            const latest = items[0];
            return [{
                id: uid,
                topic: latest.topic,
                summary: latest.summary,
                user: uid,
                status: latest.status,
                time: latest.time,
                _sort: latest._sort,
            }, ];
        };

        const loadAdminHistory = async () => {
            if (!adminEndpoint) {
                return [];
            }
            const response = await fetch(adminEndpoint, {
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

        const initHistory = async () => {
            setState({
                loading: true,
                empty: false,
                error: false
            });

            if (historyMode === 'admin') {
                try {
                    const rawItems = await loadAdminHistory();
                    if (!rawItems.length) {
                        setState({
                            loading: false,
                            empty: true,
                            error: false
                        });
                        return;
                    }

                    const grouped = rawItems.reduce((acc, data) => {
                        const sessionId = data.sessionId || 'unknown';
                        const dateValue = normalizeDate(data.createdAt || data.time || data.updatedAt);
                        const timeValue = dateValue ? dateValue.getTime() : 0;
                        const textValue = typeof data.text === 'string' ? data.text.trim() : '';
                        const topicFromText = textValue ?
                            `${textValue.slice(0, 64)}${textValue.length > 64 ? '...' : ''}` : '';
                        const summaryFromText = textValue ?
                            `${textValue.slice(0, 160)}${textValue.length > 160 ? '...' : ''}` : '';

                        if (!acc[sessionId]) {
                            acc[sessionId] = {
                                sessionId,
                                count: 0,
                                latestTime: 0,
                                latestRole: '',
                                topic: '',
                                summary: '',
                                timeLabel: '-',
                            };
                        }

                        const entry = acc[sessionId];
                        entry.count += 1;

                        if (timeValue >= entry.latestTime) {
                            entry.latestTime = timeValue;
                            entry.latestRole = data.role || '';
                            entry.topic = data.topic || data.title || data.subject || topicFromText ||
                                'Topik belum tersedia';
                            entry.summary = data.summary || data.lastMessage || data.preview ||
                                summaryFromText || '';
                            entry.timeLabel = formatRelative(dateValue);
                        }

                        return acc;
                    }, {});

                    const items = Object.values(grouped).map((entry) => ({
                        id: entry.sessionId,
                        topic: entry.topic,
                        summary: entry.summary,
                        user: entry.sessionId,
                        status: entry.latestRole ? `Last: ${entry.latestRole}` : 'Selesai',
                        time: entry.timeLabel,
                        _sort: entry.latestTime,
                    }));

                    items.sort((a, b) => b._sort - a._sort);
                    cachedItems = items;
                    currentPage = 1;
                    renderPage();
                    setState({
                        loading: false,
                        empty: false,
                        error: false
                    });
                    return;
                } catch (err) {
                    if (errorEl) errorEl.textContent = 'Gagal memuat riwayat admin.';
                    setState({
                        loading: false,
                        empty: false,
                        error: true
                    });
                    return;
                }
            }

            if (!firebaseConfig?.apiKey) {
                if (errorEl) errorEl.textContent = 'Konfigurasi Firebase belum diisi.';
                setState({
                    loading: false,
                    empty: false,
                    error: true
                });
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
                        setState({
                            loading: false,
                            empty: true,
                            error: false
                        });
                        return;
                    }
                    try {
                        const items = await loadHistory(user.uid);
                        if (!items.length) {
                            setState({
                                loading: false,
                                empty: true,
                                error: false
                            });
                            return;
                        }
                        cachedItems = items;
                        currentPage = 1;
                        renderPage();
                        setState({
                            loading: false,
                            empty: false,
                            error: false
                        });
                    } catch (err) {
                        if (errorEl) errorEl.textContent = 'Gagal memuat riwayat chat.';
                        setState({
                            loading: false,
                            empty: false,
                            error: true
                        });
                    }
                });
            } catch (err) {
                if (errorEl) errorEl.textContent = 'Autentikasi Firebase gagal.';
                setState({
                    loading: false,
                    empty: false,
                    error: true
                });
            }
        };

        initHistory();

        if (paginationControlsEl) {
            paginationControlsEl.addEventListener('click', (event) => {
                const target = event.target;
                if (!target || !target.dataset?.page) return;
                const nextPage = Number(target.dataset.page);
                if (Number.isNaN(nextPage) || nextPage < 1) return;
                currentPage = nextPage;
                renderPage();
            });
        }
    </script>
@endsection
