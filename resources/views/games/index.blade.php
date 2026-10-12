@extends('layouts.arcade', ['title' => 'Koleksi Game - Game Archive'])

{{-- Halaman lebar, sama seperti landing dan About --}}
@section('wide', '1')

{{-- Memakai gaya landing (rt.css), ditambah sedikit gaya khusus halaman ini --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/arcade/rt.css') }}?v={{ filemtime(public_path('css/arcade/rt.css')) }}">
    <style>
        .ga-hero{margin-bottom:36px}
        .ga-hero .rt-actions{margin-top:30px}
        .ga-tools{display:flex;flex-wrap:wrap;gap:14px;padding:0 6px;margin-bottom:30px}
        .ga-tools .grow-2{flex:2 1 260px}
        .ga-tools .grow-1{flex:1 1 180px}

        /* Kolom isian */
        .ga-input{height:42px;padding:0 14px;font-family:var(--pixel);font-size:10px;color:#f1eefc;background-color:#1a1738;border:2px solid var(--line);border-radius:0}
        .ga-input::placeholder{color:#9a95bd}
        .ga-input:focus{color:#fff;background-color:#1a1738;border-color:#22e4f2;box-shadow:4px 4px 0 #0f0d20}
        .ga-input.is-invalid{border-color:#ff7a95;background-image:none;box-shadow:none}
        .ga-input[type=file]{height:auto;padding:9px 12px}
        .ga-input[type=date]{color-scheme:dark}
        .ga-input option{font-family:var(--pixel);color:#f1eefc;background:#1a1738}
        .ga-input[type=file]::file-selector-button{margin-right:12px;padding:8px 12px;font-family:var(--pixel);font-size:9px;text-transform:uppercase;color:#fff;background:#2a2158;border:2px solid var(--purple-hi);border-radius:0;cursor:pointer}
        .ga-label{display:block;margin:0 0 8px;font:8px/1.4 var(--pixel);text-transform:uppercase;letter-spacing:1px;color:var(--muted)}
        .ga-fb{display:none;margin-top:6px;font:8px/1.6 var(--pixel);color:#ff7a95}
        .ga-input.is-invalid ~ .ga-fb{display:block}
        .ga-hint{margin-top:6px;font:8px/1.6 var(--pixel);color:var(--muted)}

        /* Tombol kecil (Edit, Hapus, Batal) */
        .ga-btn{padding:10px 14px;font:9px/1 var(--pixel);text-transform:uppercase;letter-spacing:1px;color:#fff;background:#1a1738;border:2px solid var(--purple-hi);border-radius:0;box-shadow:4px 4px 0 #0f0d20;cursor:pointer;transition:transform .08s,box-shadow .08s,background .15s,color .15s}
        .ga-btn:hover{background:#2a2158;color:#fff}
        .ga-btn:active{transform:translate(3px,3px);box-shadow:1px 1px 0 #0f0d20}
        .ga-btn-edit{color:#22e4f2;border-color:#22e4f2}
        .ga-btn-edit:hover{background:#22e4f2;color:#0f0d20}
        .ga-btn-del{color:#ff7a95;border-color:#ff7a95}
        .ga-btn-del:hover{background:#ff7a95;color:#0f0d20}
        .ga-danger{background:#ff7a95;color:#0f0d20;box-shadow:5px 5px 0 #0f0d20}
        .ga-danger:hover{background:#ff93a8;color:#0f0d20}

        /* Kartu game */
        .ga-card{display:flex;flex-direction:column;height:100%}
        .ga-cover{aspect-ratio:16/10;margin-bottom:18px;overflow:hidden;background:#0c0a20;border:2px solid var(--border)}
        .ga-cover img{display:block;width:100%;height:100%;object-fit:cover}
        .ga-cover .noimg{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;width:100%;height:100%;font:8px var(--pixel);color:var(--muted)}
        .ga-cover .noimg span{font-size:24px;opacity:.6}
        .ga-genre{align-self:flex-start;margin-bottom:14px;padding:7px 10px;font:8px/1.4 var(--pixel);text-transform:uppercase;color:#e9e0ff;background:#1a1738;border:1px solid var(--line)}
        .ga-card .rt-card-text{margin-bottom:6px}
        .ga-price{margin:12px 0 18px;font:11px/1.4 var(--pixel);color:var(--green);text-shadow:0 0 8px rgba(61,220,132,.45)}
        .ga-actions{display:flex;gap:14px;margin-top:auto}
        .ga-empty{grid-column:1/-1;padding:48px 16px;text-align:center;font:9.5px/1.8 var(--pixel);color:var(--muted);border:2px dashed var(--line)}
        .ga-alert{margin:0 6px 24px;padding:14px 16px;font:9px/1.7 var(--pixel);border:2px solid}
        .ga-alert.success{color:var(--green);border-color:var(--green);background:rgba(61,220,132,.08)}
        .ga-alert.danger{color:#ff7a95;border-color:#ff7a95;background:rgba(255,122,149,.08)}

        /* Popup: bingkai ganda seperti panel landing */
        .ga-modal .modal-content{background:rgba(15,12,36,.98);border:3px solid var(--purple-hi);border-radius:0;color:#f1eefc;box-shadow:0 0 0 3px #0f0d20,0 0 0 6px var(--border),10px 10px 0 6px rgba(0,0,0,.4)}
        .ga-modal .modal-header{padding:22px 24px 16px;border-bottom:2px solid var(--line);border-radius:0}
        .ga-modal .modal-title{font:12px/1.5 var(--pixel);text-transform:uppercase;color:#fff;text-shadow:2px 2px 0 var(--purple)}
        .ga-modal .modal-body{padding:22px 24px}
        .ga-modal .modal-footer{padding:16px 24px 24px;border-top:2px solid var(--line);gap:14px}
        .ga-modal .btn-close{filter:invert(1)}
        .ga-modal .rt-btn{min-width:130px;padding:14px 18px}
        .ga-modal .preview{display:none;width:100%;max-height:200px;object-fit:cover;margin-bottom:10px;border:2px solid var(--border)}
        .ga-modal .preview.show{display:block}
        .ga-modal .form-check-label{font:8px/1.6 var(--pixel);color:var(--muted)}
        .ga-modal .ga-msg{margin:0;font:9.5px/1.8 var(--pixel);color:var(--muted)}
        .ga-modal .ga-msg strong{color:#fff}
    </style>
@endpush

@section('content')
<div class="rt">

    {{-- HEADER (gaya hero landing) --}}
    <section class="rt-panel rt-hero rt-hero-sm ga-hero">
        <p class="rt-blink">&#9654; MY COLLECTION &#9664;</p>
        <h1 class="rt-title">Koleksi Game</h1>
        <p class="rt-lead" id="count">Memuat data...</p>
        <div class="rt-actions">
            <button type="button" class="rt-btn" id="btnAdd">+ Tambah game</button>
        </div>
    </section>

    <div id="alertBox"></div>

    {{-- Cari dan filter --}}
    <div class="ga-tools">
        <input type="search" id="search" class="form-control ga-input grow-2" placeholder="Cari nama, developer, atau genre..." aria-label="Cari game">
        <select id="filterGenre" class="form-select ga-input grow-1" aria-label="Filter genre"></select>
    </div>

    {{-- READ: daftar game --}}
    <section class="rt-section">
        <div class="rt-grid" id="list"></div>
    </section>

    <p class="rt-foot">&copy; {{ date('Y') }} KELOMPOK 1 &bull; PEMROGRAMAN APLIKASI WEB</p>
</div>

{{-- CREATE & UPDATE: satu form dipakai berdua --}}
<div class="modal fade ga-modal" id="formModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form class="modal-content" id="gameForm" novalidate>
            <div class="modal-header">
                <h2 class="modal-title" id="formTitle">Tambah game</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="gameId">

                <div class="mb-3">
                    <label class="ga-label" for="gambar">Gambar / cover (opsional)</label>
                    <img id="preview" class="preview" alt="Pratinjau gambar">
                    <input type="file" class="form-control ga-input" id="gambar" accept="image/png,image/jpeg,image/webp,image/gif">
                    <div class="ga-fb"></div>
                    <div class="ga-hint">JPG, PNG, WEBP, atau GIF. Maksimal 5 MB.</div>
                    <div class="form-check mt-2 d-none" id="removeWrap">
                        <input class="form-check-input" type="checkbox" id="hapusGambar">
                        <label class="form-check-label" for="hapusGambar">Hapus gambar saat ini</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="ga-label" for="nama">Nama</label>
                    <input class="form-control ga-input" id="nama" required placeholder="Contoh: God of War Ragnarok">
                    <div class="ga-fb"></div>
                </div>
                <div class="mb-3">
                    <label class="ga-label" for="developer">Developer</label>
                    <input class="form-control ga-input" id="developer" required placeholder="Contoh: Santa Monica Studio">
                    <div class="ga-fb"></div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-sm-5">
                        <label class="ga-label" for="tahun">Tahun</label>
                        <input type="number" class="form-control ga-input" id="tahun" min="1950" max="2100" required placeholder="2022">
                        <div class="ga-fb"></div>
                    </div>
                    <div class="col-sm-7">
                        <label class="ga-label" for="rilis">Tanggal rilis</label>
                        <input type="date" class="form-control ga-input" id="rilis" required>
                        <div class="ga-fb"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="ga-label" for="genre">Genre</label>
                    <input class="form-control ga-input" id="genre" list="genreList" required placeholder="Pilih atau ketik genre">
                    <datalist id="genreList">
                        <option value="Action"><option value="Action-Adventure"><option value="Adventure">
                        <option value="RPG"><option value="Strategy"><option value="Simulation">
                        <option value="Puzzle"><option value="Sports"><option value="Horror">
                    </datalist>
                    <div class="ga-fb"></div>
                </div>
                <div>
                    <label class="ga-label" for="harga">Harga (Rp)</label>
                    <input type="number" class="form-control ga-input" id="harga" min="0" step="1000" required placeholder="879000">
                    <div class="ga-fb"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="ga-btn" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="rt-btn" id="btnSave">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- DELETE: konfirmasi sebelum hapus --}}
<div class="modal fade ga-modal" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Hapus game?</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p class="ga-msg">Kamu akan menghapus <strong id="delName"></strong>. Data yang dihapus tidak bisa dikembalikan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="ga-btn" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="rt-btn ga-danger" id="confirmDelete">Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Script Bootstrap dimuat layout SETELAH bagian ini, jadi seluruh kode dijalankan setelah halaman siap
    document.addEventListener('DOMContentLoaded', () => {
        // Alamat API milik backend (GameController, apiResource)
        const API = @json(url('/games'));
        const LOGIN = @json(url('/login'));
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        const FIELDS = ['nama', 'developer', 'tahun', 'rilis', 'genre', 'harga'];   // field teks
        const IMG_FIELD = 'gambar';          // nama field file yang dikirim ke backend
        const MAX_IMG = 5 * 1024 * 1024;     // 5 MB (sama dengan backend: max:5120)
        const ALL_INPUTS = [...FIELDS, IMG_FIELD];
        const $ = id => document.getElementById(id);

        // Pindahkan modal ke <body> supaya tidak tertutup lapisan gelap (backdrop)
        document.body.appendChild($('formModal'));
        document.body.appendChild($('deleteModal'));
        const formModal = new bootstrap.Modal($('formModal'));
        const deleteModal = new bootstrap.Modal($('deleteModal'));

        let games = [];
        let deleteId = null;
        let currentImg = null;   // URL gambar milik game yang sedang diedit

        const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        const rupiah = n => 'Rp ' + Number(n).toLocaleString('id-ID');
        const tanggal = d => d ? new Date(String(d).slice(0, 10) + 'T00:00:00').toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'}) : '-';

        // Jika backend hanya menyimpan PATH (mis. "games/abc.jpg"), isi alamat dasar bucket Supabase yang PUBLIC di sini.
        // Kosongkan jika backend sudah mengirim URL lengkap.
        const STORAGE_BASE = '';
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

        // Satu fungsi untuk semua permintaan ke API. body boleh berupa FormData (upload gambar).
        async function api(method, url, body) {
            const headers = {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest'
            };
            const res = await fetch(url, { method, headers, body });   // FormData: browser mengisi Content-Type sendiri
            if (res.status === 401 || res.status === 419) { window.location = LOGIN; throw new Error('Sesi habis'); }
            const data = await res.json().catch(() => ({}));
            if (!res.ok) {
                const err = new Error((data.message || 'Terjadi kesalahan') + (data.error ? ' (' + data.error + ')' : ''));
                err.errors = data.errors || {};
                throw err;
            }
            return data;
        }

        /* ===== READ ===== */
        async function loadGames() {
            try {
                games = await api('GET', API);
                refresh();
            } catch (e) {
                $('count').textContent = 'Gagal memuat data';
                notify(e.message, 'danger');
            }
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
            $('list').innerHTML = rows.map(x => `
                <article class="rt-card ga-card">
                    ${cover(x)}
                    <h3 class="rt-card-title">${esc(x.nama)}</h3>
                    <span class="ga-genre">${esc(x.genre)}</span>
                    <p class="rt-card-text">Developer: ${esc(x.developer)}</p>
                    <p class="rt-card-text">Tahun: ${esc(x.tahun)}</p>
                    <p class="rt-card-text">Rilis: ${esc(tanggal(x.rilis))}</p>
                    <p class="ga-price">${rupiah(x.harga)}</p>
                    <div class="ga-actions">
                        <button type="button" class="ga-btn ga-btn-edit" data-act="edit" data-id="${x.id}">Edit</button>
                        <button type="button" class="ga-btn ga-btn-del" data-act="del" data-id="${x.id}">Hapus</button>
                    </div>
                </article>`).join('');
        }
        function refresh() { fillGenreFilter(); render(); }

        /* ===== Form ===== */
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

        // Pratinjau file yang baru dipilih + validasi ukuran/tipe di sisi klien
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

        /* ===== CREATE ===== */
        function openAdd() {
            FIELDS.forEach(f => { $(f).value = ''; });
            $(IMG_FIELD).value = '';
            $('hapusGambar').checked = false;
            $('removeWrap').classList.add('d-none');
            currentImg = null;
            setPreview(null);
            $('gameId').value = '';
            $('formTitle').textContent = 'Tambah game';
            clearErrors();
            formModal.show();
        }
        $('btnAdd').addEventListener('click', openAdd);

        /* ===== UPDATE: buka form berisi data lama ===== */
        function openEdit(id) {
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
            formModal.show();
        }

        // Simpan: POST /games jika id kosong; jika id terisi, POST /games/{id} + _method=PUT
        // (PHP tidak membaca file dari request PUT asli, jadi multipart dikirim lewat POST + method spoofing)
        $('gameForm').addEventListener('submit', async e => {
            e.preventDefault();
            clearErrors();
            const fd = new FormData();
            FIELDS.forEach(f => fd.append(f, $(f).value.trim()));
            const file = $(IMG_FIELD).files[0];
            if (file) fd.append(IMG_FIELD, file);
            else if ($('hapusGambar').checked) fd.append(IMG_FIELD, '');   // gambar kosong -> backend jadikan NULL
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
                if (Object.keys(err.errors || {}).length) showErrors(err.errors);   // error validasi dari server (422)
                else notify(err.message, 'danger');
            } finally {
                $('btnSave').disabled = false;
            }
        });

        /* ===== DELETE ===== */
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

        // Tombol Edit / Hapus pada kartu
        $('list').addEventListener('click', e => {
            const b = e.target.closest('[data-act]');
            if (!b) return;
            const id = Number(b.dataset.id);
            if (b.dataset.act === 'edit') openEdit(id); else openDelete(id);
        });

        $('search').addEventListener('input', render);
        $('filterGenre').addEventListener('change', render);
        loadGames();
    });
</script>
@endsection