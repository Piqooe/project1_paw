@extends('layouts.arcade', ['title' => 'About - Game Archive'])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/arcade/rt.css') }}?v={{ filemtime(public_path('css/arcade/rt.css')) }}">
@endpush

@section('wide', '1')

@php
    $info = [
        'Mata Kuliah' => 'Pemrograman Aplikasi Web',
        'Dosen Pengampu' => 'Djoko Pramono S.T.,M.Kom.',
        'Kelas' => 'Kelas A',
        'Tahun Ajaran' => '2026/2027',
    ];

    $memberGroups = [
        [
            'key' => 'frontend',
            'title' => 'Frontend Team',
            'description' => 'Tampilan, interaksi, dan pengalaman pengguna',
            'code' => 'FE',
            'members' => [
                ['name' => 'Ahmad Roja Hakam', 'nim' => '255150701111005', 'task' => 'Login & Logout', 'foto' => 'image/foto_team/elaina.jpg'],
                ['name' => 'Farras Hariz Ibrahim', 'nim' => '255150707111010', 'task' => 'CRUD Interface', 'foto' => 'image/foto_team/ayes.jpeg'],
            ],
        ],
        [
            'key' => 'backend',
            'title' => 'Backend Team',
            'description' => 'Logika aplikasi, autentikasi, dan pengelolaan data',
            'code' => 'BE',
            'members' => [
                ['name' => 'Muhammad Qalbi Al Arsyad', 'nim' => '255150707111005', 'task' => 'CRUD System', 'foto' => 'image/foto_team/qalbi.jpeg'],
                ['name' => 'Muhammad Fiqo Kholasul Fuad', 'nim' => '255150701111028', 'task' => 'Login & Logout', 'foto' => 'image/foto_team/tes_aja.jpeg'],
            ],
        ],
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
            <div class="rt-team-list">
                @foreach ($memberGroups as $group)
                    <section class="rt-team rt-team-{{ $group['key'] }}">
                        <header class="rt-team-head">
                            <span class="rt-team-icon" aria-hidden="true">{{ $group['code'] }}</span>
                            <div class="rt-team-copy">
                                <h3>{{ $group['title'] }}</h3>
                                <p>{{ $group['description'] }}</p>
                            </div>
                            <span class="rt-team-count">{{ count($group['members']) }} Players</span>
                        </header>

                        <div class="rt-team-grid">
                            @foreach ($group['members'] as $i => $m)
                                <article class="rt-card rt-player">
                                    <span class="rt-tag">{{ $group['code'] }}-0{{ $i + 1 }}</span>
                                    <div class="rt-avatar">
                                        @if (!empty($m['foto']))
                                            <img src="{{ $m['foto'] }}" alt="{{ $m['name'] }}">
                                        @else
                                            {{ strtoupper(mb_substr($m['name'], 0, 1)) }}
                                        @endif
                                    </div>
                                    <h4 class="rt-name">{{ $m['name'] }}</h4>
                                    <p class="rt-nim-label">NIM</p>
                                    <p class="rt-nim">{{ $m['nim'] }}</p>
                                    <span class="rt-role">{{ $m['task'] }}</span>
                                </article>
                            @endforeach
                        </div>
                    </section>
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