@extends('layouts.arcade', ['title' => 'Koleksi Game — Kelompok 1'])

@section('content')
{{-- Bootstrap & font dipanggil di sini karena layouts/arcade.blade.php belum memuatnya --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">

<style>
    /* Style halaman dibatasi di .ga supaya tidak merusak arcade.css */
    .ga{--bg:#120F2B;--surface:#1E1842;--primary:#6D3FC9;--accent:#3FC98F;--moon:#F3D9A8;
        --text:#F5F0E6;--text2:#A9A0CC;--pink:#FF4FA8;--border:#3A2F6E;
        --grad:linear-gradient(90deg,#5B34B5,#B03CC4);
        background:var(--bg);color:var(--text);font-family:'Inter',sans-serif;
        border:1px solid var(--border);border-radius:12px;padding:1.5rem;margin:1rem auto;max-width:1100px}
    .ga .font-pixel{font-family:'Press Start 2P',monospace;line-height:1.6}
    .ga .font-retro{font-family:'VT323',monospace;font-size:1.25rem}
    .ga .meta{color:var(--text2)}
    .ga .btn-brand{background:var(--grad);border:0;color:#fff;font-weight:700}
    .ga .btn-brand:hover{filter:brightness(1.12);color:#fff}
    .ga .btn-outline-accent{border:1px solid var(--accent);color:var(--accent)}
    .ga .btn-outline-accent:hover{background:var(--accent);color:var(--bg)}
    .ga .btn-outline-pink{border:1px solid var(--pink);color:var(--pink)}
    .ga .btn-outline-pink:hover{background:var(--pink);color:var(--bg)}
    .ga .form-control,.ga .form-select{background:var(--bg);border:1px solid var(--border);color:var(--text);padding:.7rem .9rem}
    .ga .form-control::placeholder{color:var(--text2)}
    .ga .form-control:focus,.ga .form-select:focus{background:var(--bg);color:var(--text);border-color:var(--accent);box-shadow:0 0 0 .15rem rgba(63,201,143,.2)}
    .ga .game-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;height:100%;display:flex;flex-direction:column;overflow:hidden}
    .ga .game-card .top{height:6px;background:var(--grad)}
    .ga .game-card h3{font-family:'Press Start 2P',monospace;font-size:.78rem;line-height:1.7;color:var(--moon)}
    .ga .badge-genre{background:rgba(109,63,201,.25);border:1px solid var(--primary);color:var(--text);font-weight:500}
    .ga .harga{color:var(--accent);font-weight:700}
    .ga .empty{border:2px dashed var(--border);border-radius:12px;padding:3rem 1rem;text-align:center;color:var(--text2)}

    /* Gambar di kartu */
    .ga .cover{aspect-ratio:16/10;background:#0c0a20;border-bottom:1px solid var(--border);overflow:hidden}
    .ga .cover img{width:100%;height:100%;object-fit:cover;display:block}
    .ga .cover .noimg{width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.4rem;color:var(--text2);font-family:'Press Start 2P',monospace;font-size:.6rem}
    .ga .cover .noimg span{font-size:1.6rem;opacity:.6}

    /* Modal dipindah ke <body>, jadi style-nya tidak memakai .ga */
    .modal-content{background:#1E1842;border:1px solid #6D3FC9;color:#F5F0E6;box-shadow:0 0 40px rgba(109,63,201,.25)}
    .modal-header,.modal-footer{border-color:#3A2F6E}
    .modal-title{font-family:'Press Start 2P',monospace;font-size:.85rem}
    .modal .form-control,.modal .form-select{background:#120F2B;border:1px solid #3A2F6E;color:#F5F0E6;padding:.7rem .9rem}
    .modal .form-control::placeholder{color:#A9A0CC}
    .modal .form-control:focus,.modal .form-select:focus{background:#120F2B;color:#F5F0E6;border-color:#3FC98F;box-shadow:0 0 0 .15rem rgba(63,201,143,.2)}
    .modal .form-control.is-invalid{border-color:#FF4FA8;background-image:none;box-shadow:none}
    .modal .invalid-feedback{color:#FF4FA8}
    .modal .form-label{color:#A9A0CC;font-size:.85rem}
    .modal .form-check-label{color:#A9A0CC;font-size:.85rem}
    .modal .btn-brand{background:linear-gradient(90deg,#5B34B5,#B03CC4);border:0;color:#fff;font-weight:700}
    .modal .btn-outline-brand{border:2px solid #6D3FC9;color:#F5F0E6;font-weight:700;background:transparent}
    .modal .btn-danger-brand{background:#FF4FA8;border:0;color:#120F2B;font-weight:700}
    .modal .preview{display:none;width:100%;max-height:200px;object-fit:cover;border:1px solid #3A2F6E;border-radius:8px;margin-bottom:.6rem}
    .modal .preview.show{display:block}
</style>

<div class="ga" data-bs-theme="dark">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <h1 class="font-pixel fs-5 mb-2">Koleksi Game</h1>
            <p class="font-retro mb-0 meta" id="count">Memuat data...</p>
        </div>
        <button type="button" class="btn btn-brand px-4 py-2" id="btnAdd">+ Tambah game</button>
    </div>

    <div id="alertBox"></div>

    <div class="row g-2 mb-4">
        <div class="col-md-8">
            <input type="search" id="search" class="form-control" placeholder="Cari nama, developer, atau genre..." aria-label="Cari game">
        </div>
        <div class="col-md-4">
            <select id="filterGenre" class="form-select" aria-label="Filter genre"></select>
        </div>
    </div>

    <div class="row g-3" id="list"></div>
</div>

{{-- CREATE & UPDATE: satu form dipakai berdua --}}
<div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form class="modal-content" id="gameForm" novalidate>
            <div class="modal-header">
                <h2 class="modal-title" id="formTitle">Tambah game</h2>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="gameId">

                {{-- Gambar (disimpan backend ke Supabase Storage) --}}
                <div class="mb-3">
                    <label class="form-label" for="gambar">Gambar / cover (opsional)</label>
                    <img id="preview" class="preview" alt="Pratinjau gambar">
                    <input type="file" class="form-control" id="gambar" accept="image/png,image/jpeg,image/webp,image/gif">
                    <div class="invalid-feedback"></div>
                    <div class="form-text" style="color:#A9A0CC">JPG, PNG, WEBP, atau GIF. Maksimal 5 MB.</div>
                    <div class="form-check mt-2 d-none" id="removeWrap">
                        <input class="form-check-input" type="checkbox" id="hapusGambar">
                        <label class="form-check-label" for="hapusGambar">Hapus gambar saat ini</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="nama">Nama</label>
                    <input class="form-control" id="nama" required placeholder="Contoh: God of War Ragnarok">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="developer">Developer</label>
                    <input class="form-control" id="developer" required placeholder="Contoh: Santa Monica Studio">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-sm-5">
                        <label class="form-label" for="tahun">Tahun</label>
                        <input type="number" class="form-control" id="tahun" min="1950" max="2100" required placeholder="2022">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-sm-7">
                        <label class="form-label" for="rilis">Tanggal rilis</label>
                        <input type="date" class="form-control" id="rilis" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="genre">Genre</label>
                    <input class="form-control" id="genre" list="genreList" required placeholder="Pilih atau ketik genre">
                    <datalist id="genreList">
                        <option value="Action"><option value="Action-Adventure"><option value="Adventure">
                        <option value="RPG"><option value="Strategy"><option value="Simulation">
                        <option value="Puzzle"><option value="Sports"><option value="Horror">
                    </datalist>
                    <div class="invalid-feedback"></div>
                </div>
                <div>
                    <label class="form-label" for="harga">Harga (Rp)</label>
                    <input type="number" class="form-control" id="harga" min="0" step="1000" required placeholder="879000">
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-brand" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-brand px-4" id="btnSave">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- DELETE: konfirmasi sebelum hapus --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Hapus game?</h2>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Kamu akan menghapus <strong id="delName"></strong>. Data yang dihapus tidak bisa dikembalikan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-brand" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger-brand" id="confirmDelete">Hapus</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Alamat API milik backend (GameController, apiResource)
    const API = @json(url('/games'));
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const FIELDS = ['nama', 'developer', 'tahun', 'rilis', 'genre', 'harga'];   // field teks
    const IMG_FIELD = 'gambar';          // nama field file yang dikirim ke backend
        const MAX_IMG = 5 * 1024 * 1024;     // 5 MB (sama dengan backend: max:5120)
    const $ = id => document.getElementById(id);

    // Pindahkan modal ke <body> supaya tidak tertutup backdrop (.arcade-shell punya z-index sendiri)
    document.body.appendChild($('formModal'));
    document.body.appendChild($('deleteModal'));
    const formModal = new bootstrap.Modal($('formModal'));
    const deleteModal = new bootstrap.Modal($('deleteModal'));

    let games = [];
    let deleteId = null;

    const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    const rupiah = n => 'Rp ' + Number(n).toLocaleString('id-ID');
    const tanggal = d => d ? new Date(String(d).slice(0, 10) + 'T00:00:00').toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'}) : '-';

    // Jika backend hanya menyimpan PATH (mis. "games/abc.jpg"), isi alamat dasar bucket Supabase yang PUBLIC di sini.
    // Contoh: 'https://xxxx.supabase.co/storage/v1/object/public/game-images/'  (kosongkan jika backend sudah kirim URL lengkap)
    const STORAGE_BASE = '';

    // Ambil URL gambar dari respons backend. Mencoba beberapa nama field yang umum dipakai.
    const imgUrl = x => {
        const v = x.gambar_url || x.image_url || x.url_gambar || x.gambar || x.foto || null;
        if (!v) return null;
        if (/^https?:\/\//i.test(v)) return v;
        return STORAGE_BASE ? STORAGE_BASE.replace(/\/+$/, '') + '/' + String(v).replace(/^\/+/, '') : null;
    };

    function notify(msg, type = 'success') {
        $('alertBox').innerHTML = `<div class="alert alert-${type}" role="status">${esc(msg)}</div>`;
        setTimeout(() => { $('alertBox').innerHTML = ''; }, 3000);
    }

    // Satu fungsi untuk semua permintaan ke API. body boleh berupa FormData (upload gambar).
    async function api(method, url, body) {
        const headers = {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF,
            'X-Requested-With': 'XMLHttpRequest'
        };
        // Untuk FormData, biarkan browser mengisi Content-Type (multipart + boundary)
        const res = await fetch(url, { method, headers, body });
        if (res.status === 401 || res.status === 419) { window.location = @json(url('/login')); throw new Error('Sesi habis'); }
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
        if (!url) return `<div class="cover"><div class="noimg"><span>&#127918;</span>NO IMAGE</div></div>`;
        return `<div class="cover"><img src="${esc(url)}" alt="Cover ${esc(x.nama)}" loading="lazy"
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
            $('list').innerHTML = `<div class="col-12"><div class="empty">${games.length
                ? 'Tidak ada game yang cocok dengan pencarianmu.'
                : 'Koleksimu masih kosong. Klik "Tambah game" untuk mulai.'}</div></div>`;
            return;
        }
        $('list').innerHTML = rows.map(x => `
            <div class="col-12 col-md-6 col-lg-4">
                <article class="game-card">
                    <div class="top"></div>
                    ${cover(x)}
                    <div class="p-3 d-flex flex-column flex-grow-1">
                        <h3 class="mb-2">${esc(x.nama)}</h3>
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="badge badge-genre">${esc(x.genre)}</span>
                            <span class="meta font-retro">${esc(x.tahun)}</span>
                        </div>
                        <p class="meta small mb-1">Developer: ${esc(x.developer)}</p>
                        <p class="meta small mb-1">Rilis: ${esc(tanggal(x.rilis))}</p>
                        <p class="harga flex-grow-1">${rupiah(x.harga)}</p>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-accent" onclick="openEdit(${x.id})">Edit</button>
                            <button type="button" class="btn btn-sm btn-outline-pink" onclick="openDelete(${x.id})">Hapus</button>
                        </div>
                    </div>
                </article>
            </div>`).join('');
    }
    function refresh() { fillGenreFilter(); render(); }

    /* ===== Form ===== */
    const ALL_INPUTS = [...FIELDS, IMG_FIELD];

    function clearErrors() {
        ALL_INPUTS.forEach(f => {
            $(f).classList.remove('is-invalid');
            $(f).parentElement.querySelector('.invalid-feedback').textContent = '';
        });
    }
    function showErrors(errors) {
        Object.entries(errors).forEach(([f, msgs]) => {
            if (!$(f)) return;
            $(f).classList.add('is-invalid');
            $(f).parentElement.querySelector('.invalid-feedback').textContent = msgs[0];
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
        $(IMG_FIELD).parentElement.querySelector('.invalid-feedback').textContent = '';
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

    let currentImg = null;   // URL gambar milik game yang sedang diedit

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
        // Hapus gambar: kirim 'gambar' kosong -> backend mengubahnya jadi NULL (rule nullable)
        else if ($('hapusGambar').checked) fd.append(IMG_FIELD, '');
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

    $('search').addEventListener('input', render);
    $('filterGenre').addEventListener('change', render);
    loadGames();
</script>
@endsection