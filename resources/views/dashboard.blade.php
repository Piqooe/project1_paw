@extends('layouts.arcade', ['title' => 'Dashboard - Game Archive'])

@section('wide', '1')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/arcade/Rt.css') }}?v={{ filemtime(public_path('css/arcade/Rt.css')) }}">
    <link rel="stylesheet"
        href="{{ asset('css/arcade/Dashboard.css') }}?v={{ filemtime(public_path('css/arcade/Dashboard.css')) }}">
@endpush

@section('content')
    @php
        $welcome = null;
        if (session('welcomed_sid') !== session()->getId()) {
            session(['welcomed_sid' => session()->getId()]);
            $welcome = auth()->user()->created_at->diffInMinutes(now()) < 2 ? 'new' : 'back';
        }
    @endphp

    @if ($welcome)
        <div class="db-welcome" id="dbWelcome" role="dialog" aria-label="Selamat datang">
            <div class="db-welcome-inner">
                <p class="rt-blink">&#9654; LOADING LOBBY &#9664;</p>
                <h1 class="db-welcome-title">
                    {{ $welcome === 'back' ? 'Welcome Back' : 'Welcome' }},<br>
                    <span class="db-welcome-name">{{ strtoupper(auth()->user()->name) }}</span>
                </h1>
                <p class="db-welcome-sub">MEMBUKA DASHBOARD DALAM <b id="dbCount">5</b> DETIK...</p>
            </div>
        </div>
    @endif

    <div class="rt">
        <section class="rt-panel rt-hero rt-hero-sm db-hero">
            <p class="rt-blink">&#9654; PLAYER LOBBY &#9664;</p>
            <h1 class="rt-title">Dashboard</h1>
            <p class="rt-lead">Pilih kartrij yang mau kamu kelola. Semua fitur CRUD ada di sini.</p>
        </section>

        <section class="db-stats">
            <div class="db-stat">
                <span class="db-stat-num">{{ $stats['games'] }}</span>
                <span class="db-stat-lbl">Total Game</span>
            </div>
            <div class="db-stat">
                <span class="db-stat-num">{{ $stats['platforms'] }}</span>
                <span class="db-stat-lbl">Platform</span>
            </div>
            <div class="db-stat">
                <span class="db-stat-num">{{ $stats['wishlists'] }}</span>
                <span class="db-stat-lbl">Wishlist-mu</span>
            </div>
            <div class="db-stat">
                <span class="db-stat-num">{{ $stats['reviews_mine'] }}<small>/{{ $stats['reviews_total'] }}</small></span>
                <span class="db-stat-lbl">Ulasanmu / Total</span>
            </div>
        </section>

        <section class="rt-section">
            <div class="db-grid">
                <a href="{{ route('games.page') }}" class="rt-card db-card db-card-games">
                    <div class="db-card-icon">&#127918;</div>
                    <h3 class="rt-card-title">Koleksi Game</h3>
                    <p class="db-card-desc">Kelola arsip game: tambah, edit, hapus, lengkap dengan cover dan platform.
                    </p>
                    <span class="db-card-cta">Buka Koleksi &rarr;</span>
                </a>

                <a href="{{ route('platforms.page') }}" class="rt-card db-card db-card-platforms">
                    <div class="db-card-icon">&#128250;</div>
                    <h3 class="rt-card-title">Platform</h3>
                    <p class="db-card-desc">Daftar konsol & PC: pabrikan, tahun rilis, dan jumlah game per platform.</p>
                    <span class="db-card-cta">Buka Platform &rarr;</span>
                </a>

                <a href="{{ route('wishlists.page') }}" class="rt-card db-card db-card-wishlists">
                    <div class="db-card-icon">&#128151;</div>
                    <h3 class="rt-card-title">Wishlist</h3>
                    <p class="db-card-desc">Simpan game incaran dengan prioritas, target harga diskon, dan catatan
                        pribadi.</p>
                    <span class="db-card-cta">Buka Wishlist &rarr;</span>
                </a>

                <a href="{{ route('reviews.page') }}" class="rt-card db-card db-card-reviews">
                    <div class="db-card-icon">&#9733;</div>
                    <h3 class="rt-card-title">Ulasan</h3>
                    <p class="db-card-desc">Tulis review 1-5 bintang, status main, jam main. Lihat ulasan dari komunitas.
                    </p>
                    <span class="db-card-cta">Buka Ulasan &rarr;</span>
                </a>
            </div>
        </section>

        <p class="rt-foot">&copy; {{ date('Y') }} KELOMPOK 1 &bull; PEMROGRAMAN APLIKASI WEB</p>
    </div>

    @if ($welcome)
        <script>
            (function () {
                var sisa = 5;
                var el = document.getElementById('dbCount');
                var overlay = document.getElementById('dbWelcome');
                var t = setInterval(function () {
                    sisa--;
                    if (el) el.textContent = Math.max(sisa, 0);
                    if (sisa <= 0) {
                        clearInterval(t);
                        if (overlay) {
                            overlay.classList.add('db-welcome-hide');
                            setTimeout(function () { overlay.remove(); }, 500);
                        }
                    }
                }, 1000);
            })();
        </script>
    @endif
@endsection