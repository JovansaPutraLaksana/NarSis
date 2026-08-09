<h1>Dashboard Admin Website</h1>

<p>
    Selamat datang {{ auth()->user()->name }}
</p>

<p>
    Role: {{ auth()->user()->role->label() }}
</p>


<hr>


<h2>Menu</h2>


<p>
    <a href="{{ route('website-admin.schools.index') }}">
        Kelola Sekolah
    </a>
</p>


<hr>


<form method="POST" action="{{ route('logout') }}">

    @csrf

    <button type="submit">
        Logout
    </button>

</form>