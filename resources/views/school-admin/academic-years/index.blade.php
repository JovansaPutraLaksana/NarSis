<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Tahun Ajaran</title>

</head>


<body>

<a href="{{ route('school-admin.dashboard') }}">
    ← Dashboard
</a>


<h1>
    Tahun Ajaran
</h1>


<p>
    Sekolah:
    <strong>
        {{ auth()->user()->school->name }}
    </strong>
</p>


<p>

    <a href="{{ route('school-admin.academic-years.create') }}">
        + Tambah Tahun Ajaran
    </a>

</p>


@if(session('success'))

    <div style="color:green">
        {{ session('success') }}
    </div>

@endif


@forelse($academicYears as $academicYear)

    <hr>


    <h2>

        {{ $academicYear->name }}

        @if($academicYear->is_active)

            <span style="color:green">
                [AKTIF]
            </span>

        @endif

    </h2>


    <p>
        {{ $academicYear->start_date->format('d-m-Y') }}

        sampai

        {{ $academicYear->end_date->format('d-m-Y') }}
    </p>


    @if(! $academicYear->is_active)

        <form
            method="POST"
            action="{{ route(
                'school-admin.academic-years.activate',
                $academicYear
            ) }}"
        >

            @csrf
            @method('PATCH')

            <button type="submit">
                Aktifkan Tahun Ajaran
            </button>

        </form>

    @endif


    <h3>
        Semester
    </h3>


    <table
        border="1"
        cellpadding="10"
        cellspacing="0"
    >

        <thead>

        <tr>

            <th>
                Semester
            </th>

            <th>
                Status
            </th>

            <th>
                Aksi
            </th>

        </tr>

        </thead>


        <tbody>

        @foreach($academicYear->semesters as $semester)

            <tr>

                <td>
                    {{ $semester->name }}
                </td>

                <td>

                    @if($semester->is_active)

                        <strong style="color:green">
                            Aktif
                        </strong>

                    @else

                        Tidak Aktif

                    @endif

                </td>

                <td>

                    @if(! $semester->is_active)

                        <form
                            method="POST"
                            action="{{ route(
                                'school-admin.academic-years.semesters.activate',
                                [
                                    $academicYear,
                                    $semester
                                ]
                            ) }}"
                        >

                            @csrf
                            @method('PATCH')

                            <button type="submit">
                                Aktifkan
                            </button>

                        </form>

                    @endif

                </td>

            </tr>

        @endforeach

        </tbody>

    </table>


@empty

    <p>
        Belum ada tahun ajaran.
    </p>

@endforelse


</body>

</html>