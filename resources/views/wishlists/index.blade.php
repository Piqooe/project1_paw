@extends('layouts.arcade', ['title' => 'Wishlist - Game Archive'])

@section('wide', '1')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/arcade/Rt.css') }}?v={{ filemtime(public_path('css/arcade/Rt.css')) }}">
    <link rel="stylesheet"
        href="{{ asset('css/arcade/Wishlists.css') }}?v={{ filemtime(public_path('css/arcade/Wishlists.css')) }}">
@endpush

@section('content')
    <div class="rt">
        <section class="rt-panel rt-hero rt-hero-sm ga-hero">
            <p class="rt-blink">&#9654; MY WISHLIST &#9664;</p>
            <h1 class="rt-title">Wishlist</h1>
            <p class="rt-lead" id="count">Memuat data...</p>
            <div class="rt-actions">
                <button type="button" class="rt-btn" id="btnAdd">+ Tambah ke wishlist</button>
                <a id="linkToGames" class="ga-btn" href="{{ route('games.page') }}">Lihat semua game</a>
            </div>
        </section>

        <div id="alertBox"></div>

        <div class="ga-tools">
            <input type="search" id="search" class="form-control ga-input grow-2"
                placeholder="Cari game, developer, genre, atau catatan..." aria-label="Cari wishlist">
            <select id="filterPrio" class="form-select ga-input grow-1" aria-label="Filter prioritas"></select>
        </div>

        <section class="rt-section">
            <div class="rt-grid" id="list"></div>
        </section>

        <p class="rt-foot">&copy; {{ date('Y') }} KELOMPOK 1 &bull; PEMROGRAMAN APLIKASI WEB</p>
    </div>

    <div class="modal fade ga-modal" id="formModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <form class="modal-content" id="wishlistForm" novalidate>
                <div class="modal-header">
                    <h2 class="modal-title" id="formTitle">Tambah ke wishlist</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="wishlistId">

                    <div class="mb-3" id="gameWrap">
                        <label class="ga-label" for="game_id">Pilih game</label>
                        <select class="form-select ga-input" id="game_id" required></select>
                        <div class="ga-fb" id="gameFb" style="display:none"></div>
                        <div class="ga-hint">Hanya game yang belum ada di wishlist-mu yang muncul.</div>
                    </div>

                    <div class="mb-3 wl-game-pick d-none" id="gamePreview"></div>

                    <div class="mb-3">
                        <label class="ga-label">Prioritas</label>
                        <div class="wl-prio">
                            <label class="wl-prio-opt">
                                <input type="radio" name="prioritas" value="rendah" id="prio_rendah">
                                <span class="rendah">Rendah</span>
                            </label>
                            <label class="wl-prio-opt">
                                <input type="radio" name="prioritas" value="sedang" id="prio_sedang" checked>
                                <span class="sedang">Sedang</span>
                            </label>
                            <label class="wl-prio-opt">
                                <input type="radio" name="prioritas" value="tinggi" id="prio_tinggi">
                                <span class="tinggi">Tinggi</span>
                            </label>
                        </div>
                        <input type="hidden" id="prioritas">
                        <div class="ga-fb"></div>
                    </div>

                    <div class="mb-3">
                        <label class="ga-label" for="target_harga">Target harga (opsional)</label>
                        <input type="number" class="form-control ga-input" id="target_harga" min="0" step="1000"
                            placeholder="Contoh: 150000">
                        <div class="ga-fb"></div>
                        <div class="ga-hint">Masukkan harga diskon yang kamu tunggu (dalam Rupiah).</div>
                    </div>

                    <div class="mb-3">
                        <label class="ga-label" for="catatan">Catatan (opsional)</label>
                        <textarea class="form-control ga-input" id="catatan" rows="3" maxlength="1000"
                            placeholder="Alasan kenapa pengen game ini..."></textarea>
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

    <div class="modal fade ga-modal" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Hapus dari wishlist?</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="ga-msg">Kamu akan menghapus <strong id="delName"></strong> dari wishlist. Kamu bisa
                        menambahkannya
                        kembali kapan saja.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="ga-btn" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="rt-btn ga-danger" id="confirmDelete">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.APP_WISHLISTS = {
            api: @json(url('/wishlists')),
            availableApi: @json(url('/wishlists/available-games')),
            gamesPage: @json(route('games.page')),
            login: @json(url('/login'))
        };
    </script>
    <script src="{{ asset('js/arcade/wishlists.js') }}?v={{ filemtime(public_path('js/arcade/wishlists.js')) }}"></script>
@endsection