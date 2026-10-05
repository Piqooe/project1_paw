@extends('layouts.arcade', ['title' => 'Verify Email — Kelompok 1'])

@section('content')
    <section class="cabinet">
        <div class="pixel-mascot">📬</div>
        <h1 class="cabinet-title">VERIFY YOUR ID</h1>
        <p class="cabinet-subtitle">CHECK YOUR INBOX FOR THE ACTIVATION LINK</p>

        @if (session('status') === 'verification-link-sent')
            <div class="field">
                <span class="field-error" style="color:#8bffa3">
                    ✔ A NEW ACTIVATION LINK HAS BEEN SENT TO YOUR EMAIL
                </span>
            </div>
        @endif

        <p class="footer-hint">
            BEFORE ENTERING THE ARCADE, PLEASE VERIFY YOUR PLAYER ID USING THE LINK
            WE JUST E-MAILED YOU. IF YOU DID NOT RECEIVE THE E-MAIL, YOU CAN REQUEST ANOTHER.
        </p>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="press-btn">▶ RESEND LINK</button>
        </form>

        <div class="divider">— OR —</div>

        <form method="POST" action="{{ route('logout') }}" class="logout">
            @csrf
            <button type="submit" class="link" style="background:none;border:0;cursor:pointer">◀ LOG OUT</button>
        </form>
    </section>
@endsection