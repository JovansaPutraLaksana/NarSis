<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - NarSis</title>
    <link rel="stylesheet" href="{{ asset('css/narsis.css') }}">
</head>
<body class="auth-body">
    <main class="auth-card" style="text-align:center">
        <div class="brand-mark" style="margin:0 auto 18px">N</div>
        <div class="muted" style="font-size:42px;font-weight:800">419</div>
        <h1>Sesi Berakhir</h1>
        <p class="muted">Sesi Anda telah berakhir. Silakan masuk kembali.</p>
        <div class="actions" style="justify-content:center;margin-top:20px">
            @auth
                <a class="btn btn-primary" href="{{ route('dashboard') }}">Kembali ke Dashboard</a>
            @else
                <a class="btn btn-primary" href="{{ route('login') }}">Masuk</a>
            @endauth
        </div>
    </main>
</body>
</html>
