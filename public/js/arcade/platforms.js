(() => {
    const CFG = window.APP_PLATFORMS || {};
    const API = CFG.api;
    const LOGIN = CFG.login;
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const FIELDS = ['nama', 'pabrikan', 'tahun_rilis'];
    const IMG_FIELD = 'ikon';
    const MAX_IMG = 2 * 1024 * 1024;
    const ALL_INPUTS = [...FIELDS, IMG_FIELD];
    const STORAGE_BASE = '';

    document.addEventListener('DOMContentLoaded', () => {
        const $ = id => document.getElementById(id);

        document.body.appendChild($('formModal'));
        document.body.appendChild($('deleteModal'));
        const formModal = new bootstrap.Modal($('formModal'));
        const deleteModal = new bootstrap.Modal($('deleteModal'));

        let platforms = [];
        let deleteId = null;
        let currentImg = null;

        const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        const toArray = d => Array.isArray(d) ? d : (Array.isArray(d?.data) ? d.data : []);

        const imgUrl = x => {
            const v = x.ikon_url || x.ikon || null;
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

        async function loadPlatforms() {
            try {
                platforms = toArray(await api('GET', API));
                refresh();
            } catch (e) {
                platforms = [];
                $('count').textContent = 'Gagal memuat data';
                notify(e.message, 'danger');
            }
        }

        function fillPabrikanFilter() {
            const cur = $('filterPabrikan').value;
            const pabrikans = [...new Set(platforms.map(x => x.pabrikan).filter(Boolean))].sort();
            $('filterPabrikan').innerHTML = '<option value="">Semua pabrikan</option>' +
                pabrikans.map(g => `<option ${g === cur ? 'selected' : ''}>${esc(g)}</option>`).join('');
        }

        function icon(x) {
            const url = imgUrl(x);
            if (!url) return `<div class="pf-icon"><div class="noimg"><span>&#128250;</span>NO ICON</div></div>`;
            return `<div class="pf-icon"><img src="${esc(url)}" alt="Ikon ${esc(x.nama)}" loading="lazy"
                            onerror="this.parentElement.innerHTML='<div class=&quot;noimg&quot;><span>&#9888;</span>IKON GAGAL</div>'"></div>`;
        }

        function render() {
            const q = $('search').value.trim().toLowerCase();
            const p = $('filterPabrikan').value;
            const rows = platforms.filter(x =>
                (!p || x.pabrikan === p) &&
                (!q || [x.nama, x.pabrikan].some(v => String(v || '').toLowerCase().includes(q)))
            );
            $('count').textContent = platforms.length + ' platform tersimpan';

            if (!rows.length) {
                $('list').innerHTML = `<div class="pf-empty">${platforms.length
                    ? 'Tidak ada platform yang cocok dengan pencarianmu.'
                    : 'Belum ada platform. Klik "Tambah platform" untuk mulai.'}</div>`;
                return;
            }
            $('list').innerHTML = rows.map(x => `
                    <article class="rt-card pf-card">
                        ${icon(x)}
                        <h3 class="rt-card-title">${esc(x.nama)}</h3>
                        ${x.pabrikan ? `<span class="pf-maker">${esc(x.pabrikan)}</span>` : ''}
                        <p class="rt-card-text">Tahun rilis: ${x.tahun_rilis ? esc(x.tahun_rilis) : '-'}</p>
                        <p class="pf-count">${Number(x.games_count || 0)} game terhubung</p>
                        <div class="pf-actions">
                            <button type="button" class="ga-btn ga-btn-edit" data-act="edit" data-id="${x.id}">Edit</button>
                            <button type="button" class="ga-btn ga-btn-del" data-act="del" data-id="${x.id}">Hapus</button>
                        </div>
                    </article>`).join('');
        }
        function refresh() { fillPabrikanFilter(); render(); }

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

            if (!/^image\/(png|jpe?g|webp|gif|svg\+xml)$/.test(file.type) || file.size > MAX_IMG) {
                showErrors({ [IMG_FIELD]: [file.size > MAX_IMG ? 'Ukuran ikon maksimal 2 MB.' : 'Format harus JPG, PNG, WEBP, GIF, atau SVG.'] });
                $(IMG_FIELD).value = '';
                setPreview(currentImg);
                return;
            }
            $('hapusIkon').checked = false;
            setPreview(URL.createObjectURL(file));
        });

        $('hapusIkon').addEventListener('change', () => {
            if ($('hapusIkon').checked) { $(IMG_FIELD).value = ''; setPreview(null); }
            else setPreview(currentImg);
        });

        function openAdd() {
            FIELDS.forEach(f => { $(f).value = ''; });
            $(IMG_FIELD).value = '';
            $('hapusIkon').checked = false;
            $('removeWrap').classList.add('d-none');
            currentImg = null;
            setPreview(null);
            $('platformId').value = '';
            $('formTitle').textContent = 'Tambah platform';
            clearErrors();
            formModal.show();
        }
        $('btnAdd').addEventListener('click', openAdd);

        function openEdit(id) {
            const x = platforms.find(g => g.id === id);
            if (!x) return;
            FIELDS.forEach(f => { $(f).value = x[f] ?? ''; });
            $(IMG_FIELD).value = '';
            $('hapusIkon').checked = false;
            currentImg = imgUrl(x);
            setPreview(currentImg);
            $('removeWrap').classList.toggle('d-none', !currentImg);
            $('platformId').value = x.id;
            $('formTitle').textContent = 'Edit platform';
            clearErrors();
            formModal.show();
        }

        $('platformForm').addEventListener('submit', async e => {
            e.preventDefault();
            clearErrors();
            const fd = new FormData();
            FIELDS.forEach(f => fd.append(f, $(f).value.trim()));
            const file = $(IMG_FIELD).files[0];
            if (file) fd.append(IMG_FIELD, file);
            else if ($('hapusIkon').checked) fd.append(IMG_FIELD, '');
            const id = $('platformId').value;

            $('btnSave').disabled = true;
            try {
                if (id) {
                    fd.append('_method', 'PUT');
                    await api('POST', API + '/' + id, fd);
                    notify('Perubahan disimpan');
                } else {
                    await api('POST', API, fd);
                    notify('Platform ditambahkan');
                }
                formModal.hide();
                await loadPlatforms();
            } catch (err) {
                if (Object.keys(err.errors || {}).length) showErrors(err.errors);
                else notify(err.message, 'danger');
            } finally {
                $('btnSave').disabled = false;
            }
        });

        function openDelete(id) {
            const x = platforms.find(g => g.id === id);
            if (!x) return;
            deleteId = id;
            $('delName').textContent = x.nama;
            deleteModal.show();
        }
        $('confirmDelete').addEventListener('click', async () => {
            try {
                await api('DELETE', API + '/' + deleteId);
                deleteModal.hide();
                notify('Platform dihapus');
                await loadPlatforms();
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
        $('filterPabrikan').addEventListener('change', render);
        loadPlatforms();
    });
})();
