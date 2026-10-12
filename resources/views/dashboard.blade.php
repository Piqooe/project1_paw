@extends('layouts.arcade', ['title' => 'Lobby — Kelompok 1'])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/arcade/dashboard.css') }}?v={{ filemtime(public_path('css/arcade/dashboard.css')) }}">
    <style>
        .welcome-note{font-family:var(--pixel);font-size:10px;color:var(--muted);margin:14px 0}
        .welcome-note b{color:var(--green)}
        .dash-link{display:inline-block;margin:12px 0;font-family:var(--pixel);font-size:11px;
            color:#f1eefc;text-decoration:none;border:2px solid var(--purple);padding:12px 18px}
        .dash-link:hover{background:var(--purple);color:#fff}
    </style>
@endpush

@section('content')
    @php
        // tampil sekali per sesi login: dashboard pertama setelah login/register
        $welcome = null;
        // ID sesi berganti tiap login/register, jadi welcome selalu muncul sekali setelah itu
        if (session('welcomed_sid') !== session()->getId()) {
            session(['welcomed_sid' => session()->getId()]);
            // akun yang baru dibuat < 2 menit = register, selain itu = login
            $welcome = auth()->user()->created_at->diffInMinutes(now()) < 2 ? 'new' : 'back';
        }
    @endphp
    <div class="dashboard-wrap">
        <div class="dashboard-hero">
            <h1>{{ $welcome === 'back' ? 'WELCOME BACK' : 'WELCOME' }}, {{ strtoupper(auth()->user()->name) }}</h1>

            @if($welcome)
                <p class="welcome-note">MEMBUKA KOLEKSI GAME DALAM <b id="count">5</b> DETIK...</p>
            @else
            <p>YOU ARE NOW INSIDE THE ARCHIVE. CHOOSE YOUR CARTRIDGE.</p>

            <a class="dash-link" href="{{ route('games.page') }}">&#9654; KOLEKSI GAME</a>
            <form method="POST" action="{{ route('logout') }}" class="logout">
                @csrf
                <button type="submit">◀ LOG OUT</button>
            </form>
            @endif
        </div>
    </div>

    @if($welcome)
        <script>
            (function () {
                var sisa = 5, el = document.getElementById('count');
                var t = setInterval(function () {
                    sisa--;
                    if (el) el.textContent = Math.max(sisa, 0);
                    if (sisa <= 0) {
                        clearInterval(t);
                        window.location.href = @json(route('games.page'));
                    }
                }, 1000);
            })();
        </script>
    @endif
@endsection