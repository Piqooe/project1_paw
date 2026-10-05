@extends('layouts.arcade', ['title' => 'Register — Kelompok 1'])

@section('content')
    <section class="cabinet">
        <div class="pixel-mascot">🕹️</div>
        <h1 class="cabinet-title">NEW PLAYER</h1>
        <p class="cabinet-subtitle">ENTER YOUR NAME</p>

        <form method="POST" action="{{ route('register') }}" novalidate>
            @csrf

            <div class="field">
                <label class="field-label" for="name">PLAYER TAG</label>
                <input id="name" class="field-input" type="text" name="name" value="{{ old('name') }}" placeholder="AAA"
                    maxlength="50" autocomplete="username" autofocus required>
                @error('name')
                    <span class="field-error">▲ {{ $message }}</span>
                @enderror
            </div>

            <div class="field">
                <label class="field-label" for="email">PLAYER ID</label>
                <input id="email" class="field-input" type="email" name="email" value="{{ old('email') }}"
                    placeholder="you@arcade.zone" autocomplete="email" required>
                @error('email')
                    <span class="field-error">▲ {{ $message }}</span>
                @enderror
            </div>

            <div class="field">
                <label class="field-label" for="password">ACCESS CODE</label>
                <input id="password" class="field-input" type="password" name="password"
                    placeholder="8+ CHARS, Aa, 0-9, SYMBOL" autocomplete="new-password" minlength="8" required>
                <small class="field-hint" style="opacity:.7;font-size:.75em">
                    MIN 8 CHARS · UPPER + LOWER · NUMBER · SYMBOL · NOT LEAKED
                </small>
                @error('password')
                    <span class="field-error">▲ {{ $message }}</span>
                @enderror
            </div>

            <div class="field">
                <label class="field-label" for="password_confirmation">CONFIRM CODE</label>
                <input id="password_confirmation" class="field-input" type="password" name="password_confirmation"
                    placeholder="RETYPE ACCESS CODE" autocomplete="new-password" required>
            </div>

            <button type="submit" class="press-btn">▶ INSERT COIN</button>
        </form>

        <div class="divider">— OR —</div>
        <p class="footer-hint">
            RETURNING PLAYER? <a class="link" href="{{ route('login') }}">LOGIN</a>
        </p>
    </section>
@endsection