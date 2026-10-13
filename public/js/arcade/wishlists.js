(() => {
    const CFG = window.APP_WISHLISTS || {};
    const API = CFG.api;
    const AVAILABLE_API = CFG.availableApi;
    const LOGIN = CFG.login;
    const GAMES_PAGE = CFG.gamesPage;
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const FIELDS = ['prioritas', 'target_harga', 'catatan'];
    const ALL_INPUTS = ['game_id', ...FIELDS];
    const PRIO_LABEL = { rendah: 'Rendah', sedang: 'Sedang', tinggi: 'Tinggi' };

    document.addEventListener('DOMContentLoaded', () => {
        const $ = id => document.getElementById(id);

        document.body.appendChild($('formModal'));
        document.body.appendChild($('deleteModal'));
        const formModal = new bootstrap.Modal($('formModal'));
        const deleteModal = new bootstrap.Modal($('deleteModal'));

        let wishlists = [];
        let availableGames = [];
        let deleteId = null;
        let editMode = false;

        const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        const toArray = d => Array.isArray(d) ? d : (Array.isArray(d?.data) ? d.data : []);
        const fmtRp = n => {
            const v = Number(n || 0);
            return 'Rp ' + v.toLocaleString('id-ID');
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

        async function loadWishlists() {
            try {
                wishlists = toArray(await api('GET', API));
                refresh();
            } catch (e) {
                wishlists = [];
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

        function fillPrioFilter() {
            const cur = $('filterPrio').value;
            const opts = [
                ['', 'Semua prioritas'],
                ['tinggi', 'Prioritas tinggi'],
                ['sedang', 'Prioritas sedang'],
                ['rendah', 'Prioritas rendah'],
            ];
            $('filterPrio').innerHTML = opts.map(([v, l]) =>
                `<option value="${v}" ${v === cur ? 'selected' : ''}>${l}</option>`
            ).join('');
        }

        function cover(g) {
            const url = coverUrl(g);
            if (!url) return `<div class="noimg"><span>&#127918;</span>NO COVER</div>`;
            return `<img src="${esc(url)}" alt="Cover ${esc(g.nama)}" loading="lazy"
                        onerror="this.parentElement.innerHTML='<div class=&quot;noimg&quot;><span>&#9888;</span>GAMBAR GAGAL</div>'">`;
        }

        function saveBadge(target, harga) {
            if (!target || !harga) return '';
            const t = Number(target), h = Number(harga);
            if (!(t > 0) || !(h > 0)) return '';
            if (t >= h) return `<span class="wl-save bad">Target &ge; harga</span>`;
            const pct = Math.round(((h - t) / h) * 100);
            if (pct <= 0) return '';
            return `<span class="wl-save">Hemat ${pct}%</span>`;
        }

        function platformBadges(g) {
            const list = Array.isArray(g?.platforms) ? g.platforms : [];
            if (!list.length) return '';
            return `<div class="wl-platforms">${list.map(p =>
                `<span class="wl-platform-badge">${esc(p.nama)}</span>`
            ).join('')}</div>`;
        }

        function render() {
            const q = $('search').value.trim().toLowerCase();
            const p = $('filterPrio').value;
            const rows = wishlists.filter(x => {
                if (p && x.prioritas !== p) return false;
                if (!q) return true;
                const g = x.game || {};
                return [g.nama, g.developer, g.genre, x.catatan].some(v =>
                    String(v || '').toLowerCase().includes(q)
                );
            });
            $('count').textContent = wishlists.length + ' game di wishlist-mu';

            if (!rows.length) {
                $('list').innerHTML = `<div class="wl-empty">${wishlists.length
                    ? 'Tidak ada wishlist yang cocok dengan filter.'
                    : 'Wishlist masih kosong. Klik "Tambah ke wishlist" untuk mulai.'}</div>`;
                return;
            }

            $('list').innerHTML = rows.map(x => {
                const g = x.game || {};
                return `
                    <article class="rt-card wl-card">
                        <div class="wl-cover">
                            ${cover(g)}
                            <span class="wl-badge ${esc(x.prioritas)}">${esc(PRIO_LABEL[x.prioritas] || x.prioritas)}</span>
                            ${saveBadge(x.target_harga, g.harga)}
                        </div>
                        <h3 class="rt-card-title">${esc(g.nama || '(Game tidak tersedia)')}</h3>
                        ${g.developer ? `<span class="wl-dev">${esc(g.developer)}</span>` : ''}
                        ${platformBadges(g)}
                        <div class="wl-prices">
                            <div class="original">Harga asli<strong>${g.harga != null ? fmtRp(g.harga) : '-'}</strong></div>
                            <div class="target">Target<strong>${x.target_harga != null ? fmtRp(x.target_harga) : '-'}</strong></div>
                        </div>
                        ${x.catatan ? `<p class="wl-note">${esc(x.catatan)}</p>` : ''}
                        <div class="wl-actions">
                            <button type="button" class="ga-btn ga-btn-edit" data-act="edit" data-id="${x.id}">Edit</button>
                            <button type="button" class="ga-btn ga-btn-del" data-act="del" data-id="${x.id}">Hapus</button>
                        </div>
                    </article>`;
            }).join('');
        }

        function refresh() { fillPrioFilter(); render(); }

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
        }

        function showErrors(errors) {
            Object.entries(errors).forEach(([f, msgs]) => {
                if (f === 'game_id') {
                    $('gameFb').textContent = msgs[0];
                    $('gameFb').style.display = 'block';
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
                sel.innerHTML = '<option value="">-- Tidak ada game tersedia (semua sudah di-wishlist) --</option>';
                sel.disabled = true;
                $('btnSave').disabled = true;
                return;
            }
            sel.disabled = false;
            $('btnSave').disabled = false;
            sel.innerHTML = '<option value="">-- Pilih game --</option>' +
                availableGames.map(g =>
                    `<option value="${g.id}" data-harga="${g.harga || 0}">${esc(g.nama)}${g.tahun ? ' (' + esc(g.tahun) + ')' : ''}</option>`
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
                <div class="wl-game-meta">
                    <strong>${esc(g.nama)}</strong>
                    <small>${esc(g.developer || '-')} &middot; ${esc(g.genre || '-')}</small>
                    <small>Harga asli: ${fmtRp(g.harga)}</small>
                </div>`;
        }

        async function openAdd() {
            editMode = false;
            $('wishlistId').value = '';
            $('formTitle').textContent = 'Tambah ke wishlist';
            FIELDS.forEach(f => { $(f).value = ''; });
            $('prio_sedang').checked = true;
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
            const x = wishlists.find(w => w.id === id);
            if (!x) return;
            editMode = true;
            $('wishlistId').value = x.id;
            $('formTitle').textContent = 'Edit wishlist';
            $(`prio_${x.prioritas}`).checked = true;
            $('target_harga').value = x.target_harga ?? '';
            $('catatan').value = x.catatan ?? '';
            clearErrors();
            $('gameWrap').classList.add('d-none');
            $('gamePreview').classList.remove('d-none');
            const g = x.game || {};
            const url = coverUrl(g);
            $('gamePreview').innerHTML = `
                ${url ? `<img src="${esc(url)}" alt="${esc(g.nama)}">` : `<div style="width:56px;height:56px;background:#1a1738;"></div>`}
                <div class="wl-game-meta">
                    <strong>${esc(g.nama || '-')}</strong>
                    <small>${esc(g.developer || '-')} &middot; ${esc(g.genre || '-')}</small>
                    <small>Harga asli: ${g.harga != null ? fmtRp(g.harga) : '-'}</small>
                </div>`;
            $('btnSave').disabled = false;
            formModal.show();
        }

        $('game_id').addEventListener('change', updateGamePreview);

        $('wishlistForm').addEventListener('submit', async e => {
            e.preventDefault();
            clearErrors();

            const prioritas = (document.querySelector('input[name="prioritas"]:checked') || {}).value || 'sedang';
            const payload = {
                prioritas,
                target_harga: $('target_harga').value.trim() === '' ? null : Number($('target_harga').value),
                catatan: $('catatan').value.trim() === '' ? null : $('catatan').value.trim(),
            };

            const id = $('wishlistId').value;
            $('btnSave').disabled = true;
            try {
                if (id) {
                    await api('PUT', API + '/' + id, payload);
                    notify('Wishlist diperbarui');
                } else {
                    const gameId = Number($('game_id').value);
                    if (!gameId) {
                        showErrors({ game_id: ['Pilih salah satu game.'] });
                        return;
                    }
                    payload.game_id = gameId;
                    await api('POST', API, payload);
                    notify('Game ditambahkan ke wishlist');
                }
                formModal.hide();
                await loadWishlists();
            } catch (err) {
                if (Object.keys(err.errors || {}).length) showErrors(err.errors);
                else notify(err.message, 'danger');
            } finally {
                $('btnSave').disabled = false;
            }
        });

        function openDelete(id) {
            const x = wishlists.find(w => w.id === id);
            if (!x) return;
            deleteId = id;
            $('delName').textContent = (x.game && x.game.nama) || 'wishlist ini';
            deleteModal.show();
        }

        $('confirmDelete').addEventListener('click', async () => {
            try {
                await api('DELETE', API + '/' + deleteId);
                deleteModal.hide();
                notify('Wishlist dihapus');
                await loadWishlists();
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
        $('filterPrio').addEventListener('change', render);

        if (GAMES_PAGE) {
            const link = $('linkToGames');
            if (link) link.href = GAMES_PAGE;
        }

        loadWishlists();
    });
})();
