@extends('layouts.arcade')

@section('title', 'Register - Game Archive')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/arcade/auth.css') }}?v={{ filemtime(public_path('css/arcade/auth.css')) }}">
@endpush

@section('content')
<div class="card arcade-card">
    <div class="card-header arcade-card-head">Game Archive</div>

    <div class="card-body arcade-card-body">
        <h1 class="arcade-title">Create Account</h1>
        <p class="arcade-sub">Sign up to start building your game collection.</p>

        <form method="POST" action="{{ route('register') }}" novalidate>
            @csrf

            <div class="arcade-group">
                <label for="name" class="form-label">Name</label>
                <input id="name" type="text" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" placeholder="Enter your name" required autofocus>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="arcade-group">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="Enter your email" required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="arcade-group">
                <label for="password" class="form-label">Password</label>
                <input id="password" type="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="Create a password" required autocomplete="new-password">
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="arcade-group">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation"
                       class="form-control" placeholder="Repeat your password" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn-arcade w-100">Register &rarr;</button>
        </form>

        <p class="arcade-foot">
            Already have an account? <a href="{{ route('login') }}">Login</a>
        </p>
    </div>
</div>
@endsection