<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>

<body>

<h1>Dashboard</h1>

<p>
    Selamat datang,
    <strong>{{ auth()->user()->name }}</strong>
</p>

<p>
    Email:
    {{ auth()->user()->email }}
</p>

<p>
    Role:
    {{ auth()->user()->role->label() }}
</p>

<form method="POST" action="{{ route('logout') }}">

    @csrf

    <button type="submit">
        Logout
    </button>

</form>

</body>

</html>