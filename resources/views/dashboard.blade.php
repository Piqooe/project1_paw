@extends('layouts.arcade', ['title' => 'Lobby — Kelompok 1'])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/arcade/dashboard.css') }}?v={{ filemtime(public_path('css/arcade/dashboard.css')) }}">
@endpush

@section('content')
    <div class="dashboard-wrap">
        <div class="dashboard-hero">
            <h1>WELCOME, {{ strtoupper(auth()->user()->name) }}</h1>
            <p>YOU ARE NOW INSIDE THE ARCHIVE. CHOOSE YOUR CARTRIDGE.</p>
            
            <a href="{{ route('games.page') }}" style="display:inline-block;margin:12px 0;">&#9654; KOLEKSI GAME</a>
            <form method="POST" action="{{ route('logout') }}" class="logout">
                @csrf
                <button type="submit">◀ LOG OUT</button>
            </form>
        </div>
    </div>
@endsection