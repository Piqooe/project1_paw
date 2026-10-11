@extends('layouts.arcade', ['title' => 'Game Archive - Kelompok 1'])

@section('wide', '1')

@section('content')
<div class="landing landing-center">
    <section class="landing-hero text-center">
        <p class="landing-kicker">&#9733; Kelompok 1 &#9733;</p>
        <h1 class="landing-title">Game Archive</h1>
        <p class="landing-desc">
            Simpan, kelola, dan jelajahi koleksi game favoritmu dalam satu arsip bergaya retro.
        </p>

        <div class="d-flex flex-wrap justify-content-center gap-3 mt-4">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-arcade landing-btn">Go to Dashboard &rarr;</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-arcade landing-btn">Login &rarr;</a>
                <a href="{{ route('register') }}" class="btn btn-nav landing-btn landing-btn-ghost">Register</a>
            @endauth
            <a href="{{ route('about') }}" class="btn btn-nav landing-btn landing-btn-ghost">About Us</a>
        </div>
    </section>
</div>
@endsection