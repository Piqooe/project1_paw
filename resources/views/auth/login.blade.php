@extends('layouts.arcade', ['title' => 'Login — Kelompok 1'])

@section('content')
    <section class="cabinet">
        <div class="pixel-mascot">👾</div>
        <h1 class="cabinet-title">PLAYER LOGIN</h1>
        <p class="cabinet-subtitle">PRESS START</p>

        @if (session('status'))
            <div class="flash-success">{{ session('status') }}</div>
        @endif

        @if ($errors->has('email') && !$errors->has('password'))
            <div class="flash-error">{{ $errors->first('email') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            <div class="field">
                <label class="field-label" for="email">PLAYER ID</label>
                <input id="email" class="field-input" type="email" name="email" value="{{ old('email') }}"
                    placeholder="you@arcade.zone" autocomplete="username" autofocus required>
                @error('email')
                    <span class="field-error">▲ {{ $message }}</span>
                @enderror
            </div>

            <div class="field">
                <label class="field-label" for="password">ACCESS CODE</label>
                <input id="password" class="field-input" type="password" name="password" placeholder="••••••••"
                    autocomplete="current-password" required>
                @error('password')
                    <span class="field-error">▲ {{ $message }}</span>
                @enderror
            </div>

            <div class="field-row">
                <label class="checkbox">
                    <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                    <span>SAVE PROGRESS</span>
                </label>
            </div>

            <button type="submit" class="press-btn">▶ START GAME</button>
        </form>

        <div class="divider">— OR —</div>
        <p class="footer-hint">
            NEW CHALLENGER? <a class="link" href="{{ route('register') }}">CREATE PLAYER</a>
        </p>
    </section>
@endsection