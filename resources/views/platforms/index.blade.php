@extends('layouts.arcade', ['title' => 'Platform - Game Archive'])

@section('wide', '1')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/arcade/Rt.css') }}?v={{ filemtime(public_path('css/arcade/Rt.css')) }}">
    <link rel="stylesheet"
        href="{{ asset('css/arcade/Platforms.css') }}?v={{ filemtime(public_path('css/arcade/Platforms.css')) }}">
@endpush

@section('content')
    <div class="rt">
        <section class="rt-panel rt-hero rt-hero-sm ga-hero">
            <p class="rt-blink">&#9654; MASTER DATA &#9664;</p>
            <h1 class="rt-title">Platform</h1>
            <p class="rt-lead" id="count">Memuat data...</p>
            <div class="rt-actions">
                <button type="button" class="rt-btn" id="btnAdd">+ Tambah platform</button>
            </div>
        </section>

        <div id="alertBox"></div>

        <div class="ga-tools">
            <input type="search" id="search" class="form-control ga-input grow-2" placeholder="Cari nama atau pabrikan..."
                aria-label="Cari platform">
            <select id="filterPabrikan" class="form-select ga-input grow-1" aria-label="Filter pabrikan"></select>
        </div>

        <section class="rt-section">
            <div class="rt-grid" id="list"></div>
        </section>

        <p class="rt-foot">&copy; {{ date('Y') }} KELOMPOK 1 &bull; PEMROGRAMAN APLIKASI WEB</p>
    </div>

    <div class="modal fade ga-modal" id="formModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <form class="modal-content" id="platformForm" novalidate>
                <div class="modal-header">
                    <h2 class="modal-title" id="formTitle">Tambah platform</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="platformId">

                    <div class="mb-3">
                        <label class="ga-label" for="ikon">Ikon / logo (opsional)</label>
                        <img id="preview" class="preview" alt="Pratinjau ikon">
                        <input type="file" class="form-control ga-input" id="ikon"
                            accept="image/png,image/jpeg,image/webp,image/gif,image/svg+xml">
                        <div class="ga-fb"></div>
                        <div class="ga-hint">JPG, PNG, WEBP, GIF, atau SVG. Maksimal 2 MB.</div>
                        <div class="form-check mt-2 d-none" id="removeWrap">
                            <input class="form-check-input" type="checkbox" id="hapusIkon">
                            <label class="form-check-label" for="hapusIkon">Hapus ikon saat ini</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="ga-label" for="nama">Nama platform</label>
                        <input class="form-control ga-input" id="nama" required placeholder="Contoh: PlayStation 5">
                        <div class="ga-fb"></div>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-7">
                            <label class="ga-label" for="pabrikan">Pabrikan</label>
                            <input class="form-control ga-input" id="pabrikan" list="pabrikanList"
                                placeholder="Contoh: Sony">
                            <datalist id="pabrikanList">
                                <option value="Sony">
                                <option value="Microsoft">
                                <option value="Nintendo">
                                <option value="Valve">
                                <option value="Sega">
                                <option value="Atari">
                                <option value="PC / Multi">
                            </datalist>
                            <div class="ga-fb"></div>
                        </div>
                        <div class="col-sm-5">
                            <label class="ga-label" for="tahun_rilis">Tahun rilis</label>
                            <input type="number" class="form-control ga-input" id="tahun_rilis" min="1950" max="2100"
                                placeholder="2020">
                            <div class="ga-fb"></div>
                        </div>
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
                    <h2 class="modal-title">Hapus platform?</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="ga-msg">Kamu akan menghapus <strong id="delName"></strong>. Semua kaitan dengan game akan ikut
                        terhapus dari daftar platform game tersebut.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="ga-btn" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="rt-btn ga-danger" id="confirmDelete">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.APP_PLATFORMS = {
            api: @json(url('/platforms')),
            login: @json(url('/login'))
        };
    </script>
    <script src="{{ asset('js/arcade/platforms.js') }}?v={{ filemtime(public_path('js/arcade/platforms.js')) }}"></script>
@endsection