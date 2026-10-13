(() => {
    const CFG = window.APP_GAMES || {};
    const API = CFG.api;
    const PLATFORMS_API = CFG.platformsApi;
    const LOGIN = CFG.login;
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const FIELDS = ['nama', 'developer', 'tahun', 'rilis', 'genre', 'harga'];
    const IMG_FIELD = 'gambar';
    const MAX_IMG = 5 * 1024 * 1024;
    const ALL_INPUTS = [...FIELDS, IMG_FIELD];
    const STORAGE_BASE = '';

    document.addEventListener('DOMContentLoaded', () => {
        const $ = id => document.getElementById(id);

        document.body.appendChild($('formModal'));
        document.body.appendChild($('deleteModal'));
        const formModal = new bootstrap.Modal($('formModal'));
        const deleteModal = new bootstrap.Modal($('deleteModal'));

        let games = [];
        let allPlatforms = [];
        let deleteId = null;
        let currentImg = null;

        const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        const rupiah = n => 'Rp ' + Number(n).toLocaleString('id-ID');
        const tanggal = d => d ? new Date(String(d).slice(0, 10) + 'T00:00:00').toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-';
        const toArray = d => Array.isArray(d) ? d : (Array.isArray(d?.data) ? d.data : []);

        const imgUrl = x => {
            const v = x.gambar_url || x.image_url || x.url_gambar || x.gambar || x.foto || null;
            if (!v) return null;
            if (/^https?:\/\//i.test(v)) return v;
            return STORAGE_BASE ? STORAGE_BASE.replace(/\/+$/, '') + '/' + String(v).replace(/^\/+/, '') : null;
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

        async function loadGames() {
            try {
                games = toArray(await api('GET', API));
                refresh();
            } catch (e) {
                games = [];
                $('count').textContent = 'Gagal memuat data';
                notify(e.message, 'danger');
            }
        }

        async function loadPlatforms() {
            try {
                allPlatforms = toArray(await api('GET', PLATFORMS_API));
            } catch (e) {
                allPlatforms = [];
            }
        }

        function renderPlatformChoices(selectedIds = []) {
            const box = $('platformBox');
            if (!allPlatforms.length) {
                box.innerHTML = `<div class="ga-pf-empty">Belum ada platform. Tambah dulu lewat menu Platform.</div>`;
                return;
            }
            const set = new Set(selectedIds.map(Number));
            box.innerHTML = allPlatforms.map(p => {
                const on = set.has(Number(p.id));
                return `<label class="ga-pf-chip ${on ? 'on' : ''}">
                            <input type="checkbox" name="platform_ids" value="${p.id}" ${on ? 'checked' : ''}>
                            <span>${esc(p.nama)}</span>
                        </label>`;
            }).join('');
        }

        $('platformBox').addEventListener('change', e => {
            const cb = e.target.closest('input[name="platform_ids"]');
            if (!cb) return;
            cb.closest('.ga-pf-chip').classList.toggle('on', cb.checked);
        });

        function selectedPlatformIds() {
            return [...$('platformBox').querySelectorAll('input[name="platform_ids"]:checked')]
                .map(el => el.value);
        }

        function fillGenreFilter() {
            const cur = $('filterGenre').value;
            const genres = [...new Set(games.map(x => x.genre))].sort();
            $('filterGenre').innerHTML = '<option value="">Semua genre</option>' +
                genres.map(g => `<option ${g === cur ? 'selected' : ''}>${esc(g)}</option>`).join('');
        }

        function cover(x) {
            const url = imgUrl(x);
            if (!url) return `<div class="ga-cover"><div class="noimg"><span>&#127918;</span>NO IMAGE</div></div>`;
            return `<div class="ga-cover"><img src="${esc(url)}" alt="Cover ${esc(x.nama)}" loading="lazy"
                            onerror="this.parentElement.innerHTML='<div class=&quot;noimg&quot;><span>&#9888;</span>GAMBAR GAGAL</div>'"></div>`;
        }

        function render() {
            const q = $('search').value.trim().toLowerCase();
            const g = $('filterGenre').value;
            const rows = games.filter(x =>
                (!g || x.genre === g) &&
                (!q || [x.nama, x.developer, x.genre].some(v => String(v).toLowerCase().includes(q)))
            );
            $('count').textContent = games.length + ' game tersimpan';

            if (!rows.length) {
                $('list').innerHTML = `<div class="ga-empty">${games.length
                    ? 'Tidak ada game yang cocok dengan pencarianmu.'
                    : 'Koleksimu masih kosong. Klik "Tambah game" untuk mulai.'}</div>`;
                return;
            }
            $('list').innerHTML = rows.map(x => {
                const pf = Array.isArray(x.platforms) && x.platforms.length
                    ? `<div class="ga-platforms">${x.platforms.map(p => `<span class="ga-platform-badge">${esc(p.nama)}</span>`).join('')}</div>`
                    : '';
                return `
                    <article class="rt-card ga-card">
                        ${cover(x)}
                        <h3 class="rt-card-title">${esc(x.nama)}</h3>
                        <span class="ga-genre">${esc(x.genre)}</span>
                        <p class="rt-card-text">Developer: ${esc(x.developer)}</p>
                        <p class="rt-card-text">Tahun: ${esc(x.tahun)}</p>
                        <p class="rt-card-text">Rilis: ${esc(tanggal(x.rilis))}</p>
                        <p class="ga-price">${rupiah(x.harga)}</p>
                        ${pf}
                        <div class="ga-actions">
                            <button type="button" class="ga-btn ga-btn-edit" data-act="edit" data-id="${x.id}">Edit</button>
                            <button type="button" class="ga-btn ga-btn-del" data-act="del" data-id="${x.id}">Hapus</button>
                        </div>
                    </article>`;
            }).join('');
        }
        function refresh() { fillGenreFilter(); render(); }

        function clearErrors() {
            ALL_INPUTS.forEach(f => {
                $(f).classList.remove('is-invalid');
                $(f).parentElement.querySelector('.ga-fb').textContent = '';
            });
        }
        function showErrors(errors) {
            Object.entries(errors).forEach(([f, msgs]) => {
                if (!$(f)) return;
                $(f).classList.add('is-invalid');
                $(f).parentElement.querySelector('.ga-fb').textContent = msgs[0];
            });
        }

        function setPreview(url) {
            const p = $('preview');
            if (url) { p.src = url; p.classList.add('show'); }
            else { p.removeAttribute('src'); p.classList.remove('show'); }
        }

        $(IMG_FIELD).addEventListener('change', () => {
            const file = $(IMG_FIELD).files[0];
            $(IMG_FIELD).classList.remove('is-invalid');
            $(IMG_FIELD).parentElement.querySelector('.ga-fb').textContent = '';
            if (!file) { setPreview(currentImg); return; }

            if (!/^image\/(png|jpe?g|webp|gif)$/.test(file.type) || file.size > MAX_IMG) {
                showErrors({ [IMG_FIELD]: [file.size > MAX_IMG ? 'Ukuran gambar maksimal 5 MB.' : 'Format harus JPG, PNG, WEBP, atau GIF.'] });
                $(IMG_FIELD).value = '';
                setPreview(currentImg);
                return;
            }
            $('hapusGambar').checked = false;
            setPreview(URL.createObjectURL(file));
        });

        $('hapusGambar').addEventListener('change', () => {
            if ($('hapusGambar').checked) { $(IMG_FIELD).value = ''; setPreview(null); }
            else setPreview(currentImg);
        });

        async function openAdd() {
            FIELDS.forEach(f => { $(f).value = ''; });
            $(IMG_FIELD).value = '';
            $('hapusGambar').checked = false;
            $('removeWrap').classList.add('d-none');
            currentImg = null;
            setPreview(null);
            $('gameId').value = '';
            $('formTitle').textContent = 'Tambah game';
            clearErrors();
            if (!allPlatforms.length) await loadPlatforms();
            renderPlatformChoices([]);
            formModal.show();
        }
        $('btnAdd').addEventListener('click', openAdd);

        async function openEdit(id) {
            const x = games.find(g => g.id === id);
            if (!x) return;
            FIELDS.forEach(f => { $(f).value = f === 'rilis' ? String(x.rilis).slice(0, 10) : x[f]; });
            $(IMG_FIELD).value = '';
            $('hapusGambar').checked = false;
            currentImg = imgUrl(x);
            setPreview(currentImg);
            $('removeWrap').classList.toggle('d-none', !currentImg);
            $('gameId').value = x.id;
            $('formTitle').textContent = 'Edit game';
            clearErrors();
            if (!allPlatforms.length) await loadPlatforms();
            const selected = Array.isArray(x.platforms) ? x.platforms.map(p => p.id) : [];
            renderPlatformChoices(selected);
            formModal.show();
        }

        $('gameForm').addEventListener('submit', async e => {
            e.preventDefault();
            clearErrors();
            const fd = new FormData();
            FIELDS.forEach(f => fd.append(f, $(f).value.trim()));
            const file = $(IMG_FIELD).files[0];
            if (file) fd.append(IMG_FIELD, file);
            else if ($('hapusGambar').checked) fd.append(IMG_FIELD, '');

            const pfIds = selectedPlatformIds();
            if (pfIds.length === 0) {
                fd.append('platform_ids', '');
            } else {
                pfIds.forEach(pid => fd.append('platform_ids[]', pid));
            }

            const id = $('gameId').value;

            $('btnSave').disabled = true;
            try {
                if (id) {
                    fd.append('_method', 'PUT');
                    await api('POST', API + '/' + id, fd);
                    notify('Perubahan disimpan');
                } else {
                    await api('POST', API, fd);
                    notify('Game ditambahkan');
                }
                formModal.hide();
                await loadGames();
            } catch (err) {
                if (Object.keys(err.errors || {}).length) showErrors(err.errors);
                else notify(err.message, 'danger');
            } finally {
                $('btnSave').disabled = false;
            }
        });

        function openDelete(id) {
            const x = games.find(g => g.id === id);
            if (!x) return;
            deleteId = id;
            $('delName').textContent = x.nama;
            deleteModal.show();
        }
        $('confirmDelete').addEventListener('click', async () => {
            try {
                await api('DELETE', API + '/' + deleteId);
                deleteModal.hide();
                notify('Game dihapus');
                await loadGames();
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
        $('filterGenre').addEventListener('change', render);
        loadPlatforms();
        loadGames();
    });
})();
