<h1>Dashboard Admin Website</h1>

<p>
    Selamat datang {{ auth()->user()->name }}
</p>

<p>
    Role: {{ auth()->user()->role->label() }}
</p>

<form method="POST" action="{{ route('logout') }}">
    @csrf

    <button type="submit">
        Logout
    </button>
</form>