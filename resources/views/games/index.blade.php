@extends('layouts.arcade', ['title' => 'Koleksi Game - Game Archive'])

@section('wide', '1')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/arcade/Rt.css') }}?v={{ filemtime(public_path('css/arcade/Rt.css')) }}">
    <link rel="stylesheet"
        href="{{ asset('css/arcade/Games.css') }}?v={{ filemtime(public_path('css/arcade/Games.css')) }}">
@endpush

@section('content')
    <div class="rt">
        <section class="rt-panel rt-hero rt-hero-sm ga-hero">
            <p class="rt-blink">&#9654; MY COLLECTION &#9664;</p>
            <h1 class="rt-title">Koleksi Game</h1>
            <p class="rt-lead" id="count">Memuat data...</p>
            <div class="rt-actions">
                <button type="button" class="rt-btn" id="btnAdd">+ Tambah game</button>
            </div>
        </section>

        <div id="alertBox"></div>

        <div class="ga-tools">
            <input type="search" id="search" class="form-control ga-input grow-2"
                placeholder="Cari nama, developer, atau genre..." aria-label="Cari game">
            <select id="filterGenre" class="form-select ga-input grow-1" aria-label="Filter genre"></select>
        </div>

        <section class="rt-section">
            <div class="rt-grid" id="list"></div>
        </section>

        <p class="rt-foot">&copy; {{ date('Y') }} KELOMPOK 1 &bull; PEMROGRAMAN APLIKASI WEB</p>
    </div>

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
                        <input type="file" class="form-control ga-input" id="gambar"
                            accept="image/png,image/jpeg,image/webp,image/gif">
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
                        <input class="form-control ga-input" id="developer" required
                            placeholder="Contoh: Santa Monica Studio">
                        <div class="ga-fb"></div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-sm-5">
                            <label class="ga-label" for="tahun">Tahun</label>
                            <input type="number" class="form-control ga-input" id="tahun" min="1950" max="2100" required
                                placeholder="2022">
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
                        <input class="form-control ga-input" id="genre" list="genreList" required
                            placeholder="Pilih atau ketik genre">
                        <datalist id="genreList">
                            <option value="Action">
                            <option value="Action-Adventure">
                            <option value="Adventure">
                            <option value="RPG">
                            <option value="Strategy">
                            <option value="Simulation">
                            <option value="Puzzle">
                            <option value="Sports">
                            <option value="Horror">
                        </datalist>
                        <div class="ga-fb"></div>
                    </div>
                    <div class="mb-3">
                        <label class="ga-label" for="harga">Harga (Rp)</label>
                        <input type="number" class="form-control ga-input" id="harga" min="0" step="1000" required
                            placeholder="879000">
                        <div class="ga-fb"></div>
                    </div>
                    <div>
                        <label class="ga-label">Platform (opsional, bisa pilih lebih dari satu)</label>
                        <div class="ga-pf-box" id="platformBox">
                            <div class="ga-pf-empty">Memuat platform...</div>
                        </div>
                        <div class="ga-hint">Belum ada platform? Tambah dulu di menu <strong>Platform</strong>.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="ga-btn" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="rt-btn" id="btnSave">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade ga-modal" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Hapus game?</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="ga-msg">Kamu akan menghapus <strong id="delName"></strong>. Data yang dihapus tidak bisa
                        dikembalikan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="ga-btn" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="rt-btn ga-danger" id="confirmDelete">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.APP_GAMES = {
            api: @json(url('/games')),
            platformsApi: @json(url('/platforms')),
            login: @json(url('/login'))
        };
    </script>
    <script src="{{ asset('js/arcade/games.js') }}?v={{ filemtime(public_path('js/arcade/games.js')) }}"></script>
@endsection