<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Masuk - NarSis</title><link rel="stylesheet" href="{{ asset('css/narsis.css') }}">
</head>
<body class="login-page">
<div class="login-card">
    <h1 class="login-logo">NarSis</h1><p class="login-sub">Sistem Informasi Sekolah Terintegrasi</p>
    @if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif
    <form method="POST" action="{{ route('login.store') }}">@csrf
        <div class="field"><label>Email</label><input type="email" name="email" value="{{ old('email') }}" required autofocus></div>
        <div class="field"><label>Password</label><input type="password" name="password" required></div>
        <div class="field"><label style="font-weight:400"><input style="width:auto" type="checkbox" name="remember"> Ingat saya</label></div>
        <button class="btn btn-primary" style="width:100%" type="submit">Masuk</button>
    </form>
</div>
</body>
</html>
