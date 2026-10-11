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
        ['name' => 'Ahmad Roja Hakam', '255150701111005' => '000000000001', 'role' => 'Front End - Login & Logout'],
        ['name' => 'Muhammad Qalbi Al Arsyad', 'nim' => '000000000002', 'role' => 'Back End - CRUD'],
        ['name' => 'Farras Hariz Ibrahim', 'nim' => '000000000003', 'role' => 'Front End - CRUD'],
        ['name' => 'Muhammad Fiqo Kholasul Fuad', 'nim' => '000000000004', 'role' => 'Back End - Login & Logout'],
    ];
@endphp

@section('content')
<div class="landing">

    <section class="landing-hero text-center">
        <p class="landing-kicker">&#9733; Kelompok 1 &#9733;</p>
        <h1 class="landing-title">About Us</h1>
        <p class="landing-desc">
            Game Archive adalah proyek kelompok kami: aplikasi web untuk menyimpan dan mengelola koleksi game.
        </p>
    </section>

    <section class="mb-5">
        <h2 class="landing-heading">Course Info</h2>
        <div class="info-card">
            @foreach ($info as $label => $value)
                <div class="info-row">
                    <span class="info-label">{{ $label }}</span>
                    <span class="info-value">{{ $value }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <section class="landing-team">
        <h2 class="landing-heading">Our Team</h2>

        <div class="row g-4 justify-content-center">
            @foreach ($members as $m)
                <div class="col-12 col-sm-6 col-lg-3">
                    <article class="member-card h-100">
                        <div class="member-avatar" aria-hidden="true">{{ strtoupper(mb_substr($m['name'], 0, 1)) }}</div>
                        <h3 class="member-name">{{ $m['name'] }}</h3>
                        <p class="member-nim">NIM {{ $m['nim'] }}</p>
                        <p class="member-role">{{ $m['role'] }}</p>
                    </article>
                </div>
            @endforeach
        </div>
    </section>

</div>
@endsection