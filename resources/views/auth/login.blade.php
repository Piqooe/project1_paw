@extends('layouts.arcade')

@section('title', 'Login - Game Archive')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/arcade/auth.css') }}?v={{ filemtime(public_path('css/arcade/auth.css')) }}">
@endpush

@section('content')

<div class="login-side d-none d-lg-block">
    <p class="login-side-blink">&#9654; TATAKAE!!! &#9664;</p>
    <h2 class="login-side-title">Yooo,<br>Adventurer</h2>
     <p class="login-side-text">
        Every game you own has a story. Pick up where you left off
        and keep your legendary collection safe.
    </p>
    <ul class="login-side-list">
        <li>&#9733; High score: your collection</li>
        <li>&#9881; Save slot: unlimited</li>
        <li>&#10003; Cheats: none needed</li>
    </ul>
</div>

<div class="card arcade-card">
    <div class="card-header arcade-card-head">Game Archive</div>

    <div class="card-body arcade-card-body">
        <h1 class="arcade-title">Welcome Back</h1>
        <p class="arcade-sub">Log in to access your collection and continue the adventure.</p>

        @if (session('status'))
            <div class="alert alert-success py-2 mb-3" role="alert">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            <div class="arcade-group">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="Enter your email"
                       required autofocus autocomplete="username">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="arcade-group">
                <label for="password" class="form-label">Password</label>
                <input id="password" type="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="Enter your password" required autocomplete="current-password">
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-arcade w-100">Login &rarr;</button>
        </form>

        <p class="arcade-foot">
            Don't have an account? <a href="{{ route('register') }}">Register</a>
        </p>
    </div>
</div>
@endsection