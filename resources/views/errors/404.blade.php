<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - NarSis</title>
    <link rel="stylesheet" href="{{ asset('css/narsis.css') }}">
</head>
<body class="auth-body">
    <main class="auth-card" style="text-align:center">
        <div class="brand-mark" style="margin:0 auto 18px">N</div>
        <div class="muted" style="font-size:42px;font-weight:800">404</div>
        <h1>Halaman Tidak Ditemukan</h1>
        <p class="muted">Data atau halaman yang Anda cari tidak tersedia.</p>
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
