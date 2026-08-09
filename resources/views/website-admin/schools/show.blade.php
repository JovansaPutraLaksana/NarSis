<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>{{ $school->name }}</title>

</head>


<body>

<a href="{{ route('website-admin.schools.index') }}">
    ← Kembali
</a>


<h1>
    {{ $school->name }}
</h1>


<p>
    <strong>Kode:</strong>
    {{ $school->code }}
</p>


<p>
    <strong>Alamat:</strong>
    {{ $school->address ?? '-' }}
</p>


<p>
    <strong>Telepon:</strong>
    {{ $school->phone ?? '-' }}
</p>


<p>
    <strong>Email:</strong>
    {{ $school->email ?? '-' }}
</p>


<p>
    <strong>Status:</strong>

    {{ $school->is_active ? 'Aktif' : 'Tidak Aktif' }}
</p>


<hr>


<h2>
    Pengguna Sekolah
</h2>


<table border="1" cellpadding="10">

    <thead>

    <tr>
        <th>Nama</th>
        <th>Email</th>
        <th>Role</th>
    </tr>

    </thead>


    <tbody>

    @forelse($school->users as $user)

        <tr>

            <td>
                {{ $user->name }}
            </td>

            <td>
                {{ $user->email }}
            </td>

            <td>
                {{ $user->role->label() }}
            </td>

        </tr>

    @empty

        <tr>

            <td colspan="3">
                Belum ada pengguna.
            </td>

        </tr>

    @endforelse

    </tbody>

</table>

</body>

</html>