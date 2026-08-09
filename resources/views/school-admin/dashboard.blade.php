<h1>
    Dashboard Admin Sekolah
</h1>


<p>
    Selamat datang,
    <strong>
        {{ auth()->user()->name }}
    </strong>
</p>


<p>
    Sekolah:
    <strong>
        {{ auth()->user()->school?->name ?? '-' }}
    </strong>
</p>


<p>
    Kode Sekolah:
    {{ auth()->user()->school?->code ?? '-' }}
</p>


<p>
    Role:
    {{ auth()->user()->role->label() }}
</p>


<form
    method="POST"
    action="{{ route('logout') }}"
>

    @csrf

    <button type="submit">
        Logout
    </button>

</form>