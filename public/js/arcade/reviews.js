(() => {
    const CFG = window.APP_REVIEWS || {};
    const API = CFG.api;
    const AVAILABLE_API = CFG.availableApi;
    const LOGIN = CFG.login;
    const GAMES_PAGE = CFG.gamesPage;
    const CURRENT_USER_ID = CFG.currentUserId;
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const FIELDS = ['judul', 'isi', 'jam_main'];
    const ALL_INPUTS = ['game_id', 'rating', 'status_main', ...FIELDS];
    const STATUS_LABEL = { belum: 'Belum main', sedang: 'Sedang main', tamat: 'Tamat', dropped: 'Dropped' };

    document.addEventListener('DOMContentLoaded', () => {
        const $ = id => document.getElementById(id);

        document.body.appendChild($('formModal'));
        document.body.appendChild($('deleteModal'));
        const formModal = new bootstrap.Modal($('formModal'));
        const deleteModal = new bootstrap.Modal($('deleteModal'));

        let reviews = [];
        let availableGames = [];
        let deleteId = null;
        let currentRating = 0;

        const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        const toArray = d => Array.isArray(d) ? d : (Array.isArray(d?.data) ? d.data : []);
        const fmtDate = s => {
            if (!s) return '-';
            try {
                const d = new Date(s);
                return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            } catch (_) { return s; }
        };

        const coverUrl = g => {
            const v = g?.gambar_url || g?.gambar || null;
            if (!v) return null;
            if (/^https?:\/\//i.test(v)) return v;
            return null;
        };

        function notify(msg, type = 'success') {
            $('alertBox').innerHTML = `<div class="ga-alert ${type}" role="status">${esc(msg)}</div>`;
            setTimeout(() => { $('alertBox').innerHTML = ''; }, 3000);
        }

        async function api(method, url, body) {
            const headers = {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest'
            };
            if (body && !(body instanceof FormData)) {
                headers['Content-Type'] = 'application/json';
                body = JSON.stringify(body);
            }
            const res = await fetch(url, { method, headers, body });
            if (res.status === 401 || res.status === 419) { window.location = LOGIN; throw new Error('Sesi habis'); }
            const data = await res.json().catch(() => ({}));
            if (!res.ok) {
                const err = new Error((data.message || 'Terjadi kesalahan') + (data.error ? ' (' + data.error + ')' : ''));
                err.errors = data.errors || {};
                throw err;
            }
            return data;
        }

        async function loadReviews() {
            try {
                reviews = toArray(await api('GET', API));
                refresh();
            } catch (e) {
                reviews = [];
                $('count').textContent = 'Gagal memuat data';
                notify(e.message, 'danger');
            }
        }

        async function loadAvailableGames() {
            try {
                availableGames = toArray(await api('GET', AVAILABLE_API));
            } catch (e) {
                availableGames = [];
                notify('Gagal memuat daftar game: ' + e.message, 'danger');
            }
        }

        function fillRatingFilter() {
            const cur = $('filterRating').value;
            const opts = [
                ['', 'Semua rating'],
                ['5', '&#9733;&#9733;&#9733;&#9733;&#9733; (5)'],
                ['4', '&#9733;&#9733;&#9733;&#9733; (4+)'],
                ['3', '&#9733;&#9733;&#9733; (3+)'],
                ['2', '&#9733;&#9733; (2+)'],
                ['1', '&#9733; (1+)'],
            ];
            $('filterRating').innerHTML = opts.map(([v, l]) =>
                `<option value="${v}" ${v === cur ? 'selected' : ''}>${l}</option>`
            ).join('');
        }

        function fillStatusFilter() {
            const cur = $('filterStatus').value;
            const opts = [
                ['', 'Semua status'],
                ['belum', 'Belum main'],
                ['sedang', 'Sedang main'],
                ['tamat', 'Tamat'],
                ['dropped', 'Dropped'],
            ];
            $('filterStatus').innerHTML = opts.map(([v, l]) =>
                `<option value="${v}" ${v === cur ? 'selected' : ''}>${l}</option>`
            ).join('');
        }

        function fillOwnerFilter() {
            const cur = $('filterOwner').value;
            const opts = [
                ['', 'Semua ulasan'],
                ['mine', 'Ulasan saya'],
                ['others', 'Ulasan orang lain'],
            ];
            $('filterOwner').innerHTML = opts.map(([v, l]) =>
                `<option value="${v}" ${v === cur ? 'selected' : ''}>${l}</option>`
            ).join('');
        }

        function stars(n) {
            const r = Math.max(0, Math.min(5, Number(n) || 0));
            let out = '';
            for (let i = 1; i <= 5; i++) {
                out += i <= r ? '<span>&#9733;</span>' : '<span class="off">&#9733;</span>';
            }
            return `<span class="rv-stars" aria-label="Rating ${r} dari 5">${out}</span><span class="rv-rating-num">${r}/5</span>`;
        }

        function platformBadges(g) {
            const list = Array.isArray(g?.platforms) ? g.platforms : [];
            if (!list.length) return '';
            return `<div class="rv-platforms">${list.map(p =>
                `<span class="rv-platform-badge">${esc(p.nama)}</span>`
            ).join('')}</div>`;
        }

        function render() {
            const q = $('search').value.trim().toLowerCase();
            const rMin = Number($('filterRating').value) || 0;
            const st = $('filterStatus').value;
            const own = $('filterOwner').value;

            const rows = reviews.filter(x => {
                if (rMin && Number(x.rating) < rMin) return false;
                if (st && x.status_main !== st) return false;
                if (own === 'mine' && x.user_id !== CURRENT_USER_ID) return false;
                if (own === 'others' && x.user_id === CURRENT_USER_ID) return false;
                if (!q) return true;
                const g = x.game || {};
                const u = x.user || {};
                return [g.nama, g.developer, g.genre, x.judul, x.isi, u.name].some(v =>
                    String(v || '').toLowerCase().includes(q)
                );
            });
            $('count').textContent = reviews.length + ' ulasan dari komunitas';

            if (!rows.length) {
                $('list').innerHTML = `<div class="rv-empty">${reviews.length
                    ? 'Tidak ada ulasan yang cocok dengan filter.'
                    : 'Belum ada ulasan. Jadilah yang pertama menulis ulasan!'}</div>`;
                return;
            }

            $('list').innerHTML = rows.map(x => {
                const g = x.game || {};
                const u = x.user || {};
                const mine = x.user_id === CURRENT_USER_ID;
                const hours = x.jam_main != null ? `<span class="rv-hours">${x.jam_main} jam</span>` : '';
                const actions = mine ? `
                    <div class="rv-actions">
                        <button type="button" class="ga-btn ga-btn-edit" data-act="edit" data-id="${x.id}">Edit</button>
                        <button type="button" class="ga-btn ga-btn-del" data-act="del" data-id="${x.id}">Hapus</button>
                    </div>` : '';
                return `
                    <article class="rt-card rv-card ${mine ? 'mine' : ''}">
                        <div class="rv-head">
                            <div class="rv-game">
                                <h3 class="rv-game-name">${esc(g.nama || '(Game tidak tersedia)')}</h3>
                                ${g.developer ? `<span class="rv-game-dev">${esc(g.developer)}</span>` : ''}
                            </div>
                            ${mine ? '<span class="rv-mine-tag">Milikmu</span>' : ''}
                        </div>
                        ${platformBadges(g)}
                        <div class="rv-meta">
                            ${stars(x.rating)}
                            <span class="rv-status ${esc(x.status_main)}">${esc(STATUS_LABEL[x.status_main] || x.status_main)}</span>
                            ${hours}
                        </div>
                        <p class="rv-user">Oleh <strong>${esc(u.name || 'Anonim')}</strong></p>
                        <h4 class="rv-title">${esc(x.judul)}</h4>
                        <div class="rv-body">${esc(x.isi)}</div>
                        <p class="rv-date">${fmtDate(x.created_at)}${x.updated_at && x.updated_at !== x.created_at ? ' &middot; diedit ' + fmtDate(x.updated_at) : ''}</p>
                        ${actions}
                    </article>`;
            }).join('');
        }

        function refresh() { fillRatingFilter(); fillStatusFilter(); fillOwnerFilter(); render(); }

        function clearErrors() {
            ALL_INPUTS.forEach(f => {
                const el = $(f);
                if (!el) return;
                el.classList.remove('is-invalid');
                const fb = el.parentElement.querySelector('.ga-fb');
                if (fb) fb.textContent = '';
            });
            $('gameFb').textContent = '';
            $('gameFb').style.display = 'none';
            $('ratingFb').textContent = '';
            $('ratingFb').style.display = 'none';
        }

        function showErrors(errors) {
            Object.entries(errors).forEach(([f, msgs]) => {
                if (f === 'game_id') {
                    $('gameFb').textContent = msgs[0];
                    $('gameFb').style.display = 'block';
                    return;
                }
                if (f === 'rating') {
                    $('ratingFb').textContent = msgs[0];
                    $('ratingFb').style.display = 'block';
                    return;
                }
                const el = $(f);
                if (!el) return;
                el.classList.add('is-invalid');
                const fb = el.parentElement.querySelector('.ga-fb');
                if (fb) fb.textContent = msgs[0];
            });
        }

        function renderGameOptions() {
            const sel = $('game_id');
            if (!availableGames.length) {
                sel.innerHTML = '<option value="">-- Tidak ada game tersedia (semua sudah kamu ulas) --</option>';
                sel.disabled = true;
                $('btnSave').disabled = true;
                return;
            }
            sel.disabled = false;
            $('btnSave').disabled = false;
            sel.innerHTML = '<option value="">-- Pilih game --</option>' +
                availableGames.map(g =>
                    `<option value="${g.id}">${esc(g.nama)}${g.tahun ? ' (' + esc(g.tahun) + ')' : ''}</option>`
                ).join('');
        }

        function updateGamePreview() {
            const id = Number($('game_id').value);
            const g = availableGames.find(x => x.id === id);
            const box = $('gamePreview');
            if (!g) { box.classList.add('d-none'); box.innerHTML = ''; return; }
            box.classList.remove('d-none');
            const url = coverUrl(g);
            box.innerHTML = `
                ${url ? `<img src="${esc(url)}" alt="${esc(g.nama)}">` : `<div style="width:56px;height:56px;background:#1a1738;"></div>`}
                <div class="rv-game-meta">
                    <strong>${esc(g.nama)}</strong>
                    <small>${esc(g.developer || '-')} &middot; ${esc(g.genre || '-')}</small>
                </div>`;
        }

        function renderStarPicker() {
            const box = $('starPick');
            box.innerHTML = '';
            for (let i = 1; i <= 5; i++) {
                const b = document.createElement('button');
                b.type = 'button';
                b.dataset.val = i;
                b.innerHTML = '&#9733;';
                b.setAttribute('aria-label', `Beri ${i} bintang`);
                if (i <= currentRating) b.classList.add('on');
                b.addEventListener('click', () => {
                    currentRating = i;
                    $('rating').value = i;
                    [...box.querySelectorAll('button')].forEach(btn => {
                        btn.classList.toggle('on', Number(btn.dataset.val) <= i);
                    });
                });
                box.appendChild(b);
            }
        }

        async function openAdd() {
            $('reviewId').value = '';
            $('formTitle').textContent = 'Tulis ulasan';
            FIELDS.forEach(f => { $(f).value = ''; });
            currentRating = 0;
            $('rating').value = '';
            $('status_belum').checked = true;
            renderStarPicker();
            clearErrors();
            $('gameWrap').classList.remove('d-none');
            $('gamePreview').classList.add('d-none');
            $('gamePreview').innerHTML = '';
            $('game_id').value = '';
            await loadAvailableGames();
            renderGameOptions();
            formModal.show();
        }
        $('btnAdd').addEventListener('click', openAdd);

        function openEdit(id) {
            const x = reviews.find(w => w.id === id);
            if (!x) return;
            if (x.user_id !== CURRENT_USER_ID) {
                notify('Kamu hanya bisa mengedit ulasanmu sendiri.', 'danger');
                return;
            }
            $('reviewId').value = x.id;
            $('formTitle').textContent = 'Edit ulasan';
            currentRating = Number(x.rating) || 0;
            $('rating').value = currentRating;
            $('judul').value = x.judul ?? '';
            $('isi').value = x.isi ?? '';
            $('jam_main').value = x.jam_main ?? '';
            const st = x.status_main || 'belum';
            const stRadio = $(`status_${st}`);
            if (stRadio) stRadio.checked = true;
            renderStarPicker();
            clearErrors();
            $('gameWrap').classList.add('d-none');
            $('gamePreview').classList.remove('d-none');
            const g = x.game || {};
            const url = coverUrl(g);
            $('gamePreview').innerHTML = `
                ${url ? `<img src="${esc(url)}" alt="${esc(g.nama)}">` : `<div style="width:56px;height:56px;background:#1a1738;"></div>`}
                <div class="rv-game-meta">
                    <strong>${esc(g.nama || '-')}</strong>
                    <small>${esc(g.developer || '-')} &middot; ${esc(g.genre || '-')}</small>
                </div>`;
            $('btnSave').disabled = false;
            formModal.show();
        }

        $('game_id').addEventListener('change', updateGamePreview);

        $('reviewForm').addEventListener('submit', async e => {
            e.preventDefault();
            clearErrors();

            const statusEl = document.querySelector('input[name="status_main"]:checked');
            const payload = {
                rating: Number($('rating').value) || 0,
                judul: $('judul').value.trim(),
                isi: $('isi').value.trim(),
                status_main: statusEl ? statusEl.value : 'belum',
                jam_main: $('jam_main').value.trim() === '' ? null : Number($('jam_main').value),
            };

            const id = $('reviewId').value;
            $('btnSave').disabled = true;
            try {
                if (id) {
                    await api('PUT', API + '/' + id, payload);
                    notify('Ulasan diperbarui');
                } else {
                    const gameId = Number($('game_id').value);
                    if (!gameId) {
                        showErrors({ game_id: ['Pilih salah satu game.'] });
                        return;
                    }
                    if (!payload.rating) {
                        showErrors({ rating: ['Pilih rating 1-5 bintang.'] });
                        return;
                    }
                    payload.game_id = gameId;
                    await api('POST', API, payload);
                    notify('Ulasan berhasil dibuat');
                }
                formModal.hide();
                await loadReviews();
            } catch (err) {
                if (Object.keys(err.errors || {}).length) showErrors(err.errors);
                else notify(err.message, 'danger');
            } finally {
                $('btnSave').disabled = false;
            }
        });

        function openDelete(id) {
            const x = reviews.find(w => w.id === id);
            if (!x) return;
            if (x.user_id !== CURRENT_USER_ID) {
                notify('Kamu hanya bisa menghapus ulasanmu sendiri.', 'danger');
                return;
            }
            deleteId = id;
            $('delName').textContent = (x.game && x.game.nama) || 'ulasan ini';
            deleteModal.show();
        }

        $('confirmDelete').addEventListener('click', async () => {
            try {
                await api('DELETE', API + '/' + deleteId);
                deleteModal.hide();
                notify('Ulasan dihapus');
                await loadReviews();
            } catch (err) {
                notify(err.message, 'danger');
            }
        });

        $('list').addEventListener('click', e => {
            const b = e.target.closest('[data-act]');
            if (!b) return;
            const id = Number(b.dataset.id);
            if (b.dataset.act === 'edit') openEdit(id); else openDelete(id);
        });

        $('search').addEventListener('input', render);
        $('filterRating').addEventListener('change', render);
        $('filterStatus').addEventListener('change', render);
        $('filterOwner').addEventListener('change', render);

        if (GAMES_PAGE) {
            const link = $('linkToGames');
            if (link) link.href = GAMES_PAGE;
        }

        loadReviews();
    });
})();
