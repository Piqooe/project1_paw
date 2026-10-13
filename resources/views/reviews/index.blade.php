@extends('layouts.arcade', ['title' => 'Ulasan - Game Archive'])

@section('wide', '1')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/arcade/Rt.css') }}?v={{ filemtime(public_path('css/arcade/Rt.css')) }}">
    <link rel="stylesheet"
        href="{{ asset('css/arcade/Reviews.css') }}?v={{ filemtime(public_path('css/arcade/Reviews.css')) }}">
@endpush

@section('content')
    <div class="rt">
        <section class="rt-panel rt-hero rt-hero-sm ga-hero">
            <p class="rt-blink">&#9654; COMMUNITY REVIEWS &#9664;</p>
            <h1 class="rt-title">Ulasan</h1>
            <p class="rt-lead" id="count">Memuat data...</p>
            <div class="rt-actions">
                <button type="button" class="rt-btn" id="btnAdd">+ Tulis ulasan</button>
                <a id="linkToGames" class="ga-btn" href="{{ route('games.page') }}">Lihat semua game</a>
            </div>
        </section>

        <div id="alertBox"></div>

        <div class="ga-tools">
            <input type="search" id="search" class="form-control ga-input grow-2"
                placeholder="Cari game, developer, judul ulasan, atau penulis..." aria-label="Cari ulasan">
            <select id="filterRating" class="form-select ga-input grow-1" aria-label="Filter rating"></select>
            <select id="filterStatus" class="form-select ga-input grow-1" aria-label="Filter status"></select>
            <select id="filterOwner" class="form-select ga-input grow-1" aria-label="Filter pemilik"></select>
        </div>

        <section class="rt-section">
            <div class="rt-grid" id="list"></div>
        </section>

        <p class="rt-foot">&copy; {{ date('Y') }} KELOMPOK 1 &bull; PEMROGRAMAN APLIKASI WEB</p>
    </div>

    <div class="modal fade ga-modal" id="formModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <form class="modal-content" id="reviewForm" novalidate>
                <div class="modal-header">
                    <h2 class="modal-title" id="formTitle">Tulis ulasan</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="reviewId">
                    <input type="hidden" id="rating">

                    <div class="mb-3" id="gameWrap">
                        <label class="ga-label" for="game_id">Pilih game</label>
                        <select class="form-select ga-input" id="game_id" required></select>
                        <div class="ga-fb" id="gameFb" style="display:none"></div>
                        <div class="ga-hint">Hanya game yang belum kamu ulas yang muncul.</div>
                    </div>

                    <div class="mb-3 rv-game-pick d-none" id="gamePreview"></div>

                    <div class="mb-3">
                        <label class="ga-label">Rating</label>
                        <div class="rv-star-pick" id="starPick" role="group" aria-label="Pilih rating"></div>
                        <div class="ga-fb" id="ratingFb" style="display:none"></div>
                    </div>

                    <div class="mb-3">
                        <label class="ga-label">Status main</label>
                        <div class="rv-status-pick">
                            <label class="rv-status-opt">
                                <input type="radio" name="status_main" value="belum" id="status_belum" checked>
                                <span class="belum">Belum</span>
                            </label>
                            <label class="rv-status-opt">
                                <input type="radio" name="status_main" value="sedang" id="status_sedang">
                                <span class="sedang">Sedang</span>
                            </label>
                            <label class="rv-status-opt">
                                <input type="radio" name="status_main" value="tamat" id="status_tamat">
                                <span class="tamat">Tamat</span>
                            </label>
                            <label class="rv-status-opt">
                                <input type="radio" name="status_main" value="dropped" id="status_dropped">
                                <span class="dropped">Dropped</span>
                            </label>
                        </div>
                        <div class="ga-fb"></div>
                    </div>

                    <div class="mb-3">
                        <label class="ga-label" for="judul">Judul ulasan</label>
                        <input type="text" class="form-control ga-input" id="judul" maxlength="150"
                            placeholder="Ringkasan singkat opinimu..." required>
                        <div class="ga-fb"></div>
                    </div>

                    <div class="mb-3">
                        <label class="ga-label" for="isi">Isi ulasan</label>
                        <textarea class="form-control ga-input" id="isi" rows="5" maxlength="5000"
                            placeholder="Ceritakan pengalaman main kamu..." required></textarea>
                        <div class="ga-fb"></div>
                    </div>

                    <div class="mb-3">
                        <label class="ga-label" for="jam_main">Jam main (opsional)</label>
                        <input type="number" class="form-control ga-input" id="jam_main" min="0" max="100000" step="1"
                            placeholder="Contoh: 42">
                        <div class="ga-fb"></div>
                        <div class="ga-hint">Total jam yang sudah kamu habiskan untuk game ini.</div>
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
                    <h2 class="modal-title">Hapus ulasan?</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="ga-msg">Kamu akan menghapus ulasanmu untuk <strong id="delName"></strong>. Tindakan ini
                        tidak bisa dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="ga-btn" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="rt-btn ga-danger" id="confirmDelete">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.APP_REVIEWS = {
            api: @json(url('/reviews')),
            availableApi: @json(url('/reviews/available-games')),
            gamesPage: @json(route('games.page')),
            login: @json(url('/login')),
            currentUserId: @json(auth()->id())
        };
    </script>
    <script src="{{ asset('js/arcade/reviews.js') }}?v={{ filemtime(public_path('js/arcade/reviews.js')) }}"></script>
@endsection