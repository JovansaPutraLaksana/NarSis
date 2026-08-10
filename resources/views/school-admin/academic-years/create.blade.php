<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Tahun Ajaran</title>
</head>

<body>

<a href="{{ route('school-admin.academic-years.index') }}">
    ← Kembali
</a>

<h1>Tambah Tahun Ajaran</h1>

<p>
    Sekolah:
    <strong>
        {{ auth()->user()->school->name }}
    </strong>
</p>


@if($errors->any())

    <div style="color:red">

        <ul>

            @foreach($errors->all() as $error)

                <li>
                    {{ $error }}
                </li>

            @endforeach

        </ul>

    </div>

@endif


<form
    method="POST"
    action="{{ route('school-admin.academic-years.store') }}"
>

    @csrf


    <p>

        <label>
            Tahun Ajaran
        </label>

        <br>

        <input
            type="text"
            name="name"
            value="{{ old('name') }}"
            placeholder="Contoh: 2026/2027"
            required
        >

    </p>


    <p>

        <label>
            Tanggal Mulai
        </label>

        <br>

        <input
            type="date"
            name="start_date"
            value="{{ old('start_date') }}"
            required
        >

    </p>


    <p>

        <label>
            Tanggal Selesai
        </label>

        <br>

        <input
            type="date"
            name="end_date"
            value="{{ old('end_date') }}"
            required
        >

    </p>


    <button type="submit">
        Simpan Tahun Ajaran
    </button>

</form>

</body>

</html>