<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $title ?? 'Game Archive')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Press+Start+2P&display=swap"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    @foreach (['base', 'navbar', 'buttons'] as $css)
        <link rel="stylesheet"
            href="{{ asset("css/arcade/{$css}.css") }}?v={{ filemtime(public_path("css/arcade/{$css}.css")) }}">
    @endforeach

    @stack('styles')
</head>

<body class="arcade">

    <nav class="navbar navbar-expand-md fixed-top arcade-nav">
        <div class="container-fluid arcade-nav-inner">
            <a class="navbar-brand arcade-brand" href="{{ url('/') }}">
                <svg viewBox="0 0 24 24" width="30" height="30" aria-hidden="true">
                    <path fill="#7d7d8f"
                        d="M6 6h12a4 4 0 0 1 4 4v4a4 4 0 0 1-4 4h-1l-2-2h-6l-2 2H6a4 4 0 0 1-4-4v-4a4 4 0 0 1 4-4z" />
                    <path fill="#14122b" d="M6 10h1.5V8.5H9V10h1.5v1.5H9V13H7.5v-1.5H6zM15 10h2v2h-2z" />
                </svg>
                <span>Game Archive</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">

                <ul class="navbar-nav arcade-nav-center align-items-md-center">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') || request()->is('/') ? 'active' : '' }}"
                            href="{{ url('/') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}"
                            href="{{ route('about') }}">About</a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') || request()->routeIs('games.page') || request()->routeIs('platforms.page') || request()->routeIs('wishlists.page') || request()->routeIs('reviews.page') ? 'active' : '' }}"
                                href="{{ url('/dashboard') }}">Dashboard</a>
                        </li>
                    @endauth
                </ul>

                <ul class="navbar-nav arcade-nav-right align-items-md-center">
                    @auth
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-nav btn-nav-logout">Logout</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}"
                                href="{{ route('login') }}">Login</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main
        class="stage d-flex align-items-center @hasSection('wide') stage-wide @else justify-content-center justify-content-lg-end @endif">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>