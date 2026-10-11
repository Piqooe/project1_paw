@extends('layouts.arcade', ['title' => 'About - Game Archive'])

@section('wide', '1')

@php
    $info = [
        'Mata Kuliah'    => 'Pemrograman Aplikasi Web',
        'Dosen Pengampu' => 'Djoko Pramono',
        'Kelas'          => 'Kelas A',
        'Tahun Ajaran'   => '2026/2027',
    ];

    $members = [
        ['name' => 'Ahmad Roja Hakam',            'nim' => '255150701111005', 'role' => 'Front End - Login & Logout'],
        ['name' => 'Muhammad Qalbi Al Arsyad',    'nim' => '255150707111005', 'role' => 'Back End - CRUD'],
        ['name' => 'Farras Hariz Ibrahim',        'nim' => '255150707111010', 'role' => 'Front End - CRUD'],
        ['name' => 'Muhammad Fiqo Kholasul Fuad', 'nim' => '255150701111028', 'role' => 'Back End - Login & Logout'],
    ];

    $tech = ['Laravel', 'Blade', 'Bootstrap 5', 'GitHub'];
@endphp

@section('content')
<div class="rt">

    {{-- HERO --}}
    <section class="rt-panel rt-hero rt-hero-sm">
        <p class="rt-blink">&#9733; KELOMPOK 1 &#9733;</p>
        <h1 class="rt-title">About Us</h1>
        <p class="rt-lead">
            Game Archive adalah proyek kelompok kami: aplikasi web untuk menyimpan dan mengelola koleksi game.
        </p>
    </section>

    <section class="rt-section">
        <h2 class="rt-h">Mission</h2>
        <div class="rt-grid-2">
            <article class="rt-card">
                <h3 class="rt-card-title">Tentang Proyek</h3>
                <p class="rt-card-text">
                    Aplikasi ini dibuat sebagai tugas mata kuliah. Pengguna dapat mendaftar, login,
                    lalu menyimpan dan mengelola daftar game miliknya sendiri dalam tampilan bertema retro.
                </p>
            </article>

            <article class="rt-card">
                <h3 class="rt-card-title">Course Info</h3>
                <ul class="rt-stats">
                    @foreach ($info as $label => $value)
                        <li><span>{{ $label }}</span><span>{{ $value }}</span></li>
                    @endforeach
                </ul>
            </article>
        </div>
    </section>

    <section class="rt-section">
        <h2 class="rt-h">Select Player</h2>
        <div class="rt-grid-4">
            @foreach ($members as $i => $m)
                <article class="rt-card rt-player">
                    <span class="rt-tag">P{{ $i + 1 }}</span>
                    <div class="rt-avatar" aria-hidden="true">{{ strtoupper(mb_substr($m['name'], 0, 1)) }}</div>
                    <h3 class="rt-name">{{ $m['name'] }}</h3>
                    <p class="rt-nim-label">NIM</p>
                    <p class="rt-nim">{{ $m['nim'] }}</p>
                    <span class="rt-role">{{ $m['role'] }}</span>
                </article>
            @endforeach
        </div>
    </section>

    <section class="rt-section">
        <h2 class="rt-h">Tech Stack</h2>
        <div class="rt-chips">
            @foreach ($tech as $t)
                <span class="rt-chip">{{ $t }}</span>
            @endforeach
        </div>
    </section>

    <p class="rt-foot">&copy; {{ date('Y') }} KELOMPOK 1 &bull; PEMROGRAMAN APLIKASI WEB</p>
</div>
@endsection