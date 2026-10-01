<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Kelompok 1' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="{{ asset('css/arcade.css') }}?v={{ filemtime(public_path('css/arcade.css')) }}">
</head>

<body>
    <div class="arcade-shell">
        <header class="arcade-marquee">
            <div class="arcade-logo">KELOMPOK <span>1</span> GACOR</div>
            <div class="arcade-status">ONLINE</div>
        </header>

        <main class="arcade-main">
            @yield('content')
        </main>

        <footer class="arcade-footer">
            <span>© {{ date('Y') }} CIHUY.</span>
            <span class="hi-score">HI-SCORE 999999</span>
            <span>1P READY</span>
        </footer>
    </div>
</body>

</html>