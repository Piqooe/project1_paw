@extends('layouts.arcade', ['title' => 'Game Archive - Kelompok 1'])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/arcade/rt.css') }}?v={{ filemtime(public_path('css/arcade/rt.css')) }}">
@endpush

@section('wide', '1')

@section('content')
<div class="rt">

    {{-- HERO --}}
    <section class="rt-panel rt-hero">
        <p class="rt-blink">&#9654; PRESS START &#9664;</p>
        <h1 class="rt-title">Game<br>Archive</h1>
        <p class="rt-lead">
            Simpan, kelola, dan jelajahi koleksi game favoritmu dalam satu arsip bergaya retro.
        </p>

        <div class="rt-actions">
            @auth
                <a href="{{ route('dashboard') }}" class="rt-btn">Dashboard &rarr;</a>
            @else
                <a href="{{ route('login') }}" class="rt-btn">&#9654; Login</a>
                <a href="{{ route('register') }}" class="rt-btn rt-btn-alt">Register</a>
            @endauth
            <a href="{{ route('about') }}" class="rt-btn rt-btn-alt">About Us</a>
        </div>
    </section>

    {{-- FITUR --}}
    <section class="rt-section">
        <h2 class="rt-h">Features</h2>
        <div class="rt-grid">
            <article class="rt-card">
                <div class="rt-icon">&#9733;</div>
                <h3 class="rt-card-title">Simpan Koleksi</h3>
                <p class="rt-card-text">Catat semua game yang kamu punya dalam satu arsip pribadi.</p>
            </article>
            <article class="rt-card">
                <div class="rt-icon">&#9881;</div>
                <h3 class="rt-card-title">Kelola Data</h3>
                <p class="rt-card-text">Tambah, ubah, dan hapus data game kapan saja dengan mudah.</p>
            </article>
            <article class="rt-card">
                <div class="rt-icon">&#10003;</div>
                <h3 class="rt-card-title">Akun Aman</h3>
                <p class="rt-card-text">Login dan verifikasi email menjaga koleksi tetap milikmu.</p>
            </article>
        </div>
    </section>

    {{-- CARA MAIN --}}
    <section class="rt-section">
        <h2 class="rt-h">How to Play</h2>
        <div class="rt-grid">
            <article class="rt-card">
                <div class="rt-step-no">01</div>
                <h3 class="rt-card-title">Daftar Akun</h3>
                <p class="rt-card-text">Buat akun baru lalu verifikasi email kamu.</p>
            </article>
            <article class="rt-card">
                <div class="rt-step-no">02</div>
                <h3 class="rt-card-title">Masuk Lobby</h3>
                <p class="rt-card-text">Login dan buka dashboard sebagai pusat koleksimu.</p>
            </article>
            <article class="rt-card">
                <div class="rt-step-no">03</div>
                <h3 class="rt-card-title">Tambah Game</h3>
                <p class="rt-card-text">Isi data game favoritmu dan kelola kapan saja.</p>
            </article>
        </div>
    </section>

    <p class="rt-foot">&copy; {{ date('Y') }} KELOMPOK 1 &bull; PEMROGRAMAN APLIKASI WEB</p>
</div>
@endsection