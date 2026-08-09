<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>Daftar Sekolah</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f6f8;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .button {
            background: #111827;
            color: white;
            padding: 11px 18px;
            text-decoration: none;
            border-radius: 7px;
        }

        table {
            width: 100%;
            background: white;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 14px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #f9fafb;
        }

        .success {
            padding: 12px;
            background: #dcfce7;
            color: #166534;
            margin-bottom: 20px;
            border-radius: 7px;
        }

        .active {
            color: green;
        }

        .inactive {
            color: red;
        }
    </style>

</head>

<body>

<div class="container">

    <div class="header">

        <div>
            <h1>Daftar Sekolah</h1>

            <a href="{{ route('website-admin.dashboard') }}">
                ← Dashboard
            </a>
        </div>

        <a
            href="{{ route('website-admin.schools.create') }}"
            class="button"
        >
            + Tambah Sekolah
        </a>

    </div>


    @if(session('success'))

        <div class="success">
            {{ session('success') }}
        </div>

    @endif


    <table>

        <thead>

        <tr>
            <th>Kode</th>
            <th>Nama Sekolah</th>
            <th>Email</th>
            <th>User</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>

        </thead>


        <tbody>

        @forelse($schools as $school)

            <tr>

                <td>
                    {{ $school->code }}
                </td>

                <td>
                    {{ $school->name }}
                </td>

                <td>
                    {{ $school->email ?? '-' }}
                </td>

                <td>
                    {{ $school->users_count }}
                </td>

                <td>

                    @if($school->is_active)

                        <span class="active">
                            Aktif
                        </span>

                    @else

                        <span class="inactive">
                            Tidak Aktif
                        </span>

                    @endif

                </td>

                <td>

                    <a
                        href="{{ route(
                            'website-admin.schools.show',
                            $school
                        ) }}"
                    >
                        Detail
                    </a>

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="6">
                    Belum ada sekolah.
                </td>

            </tr>

        @endforelse

        </tbody>

    </table>


    <div style="margin-top: 20px">

        {{ $schools->links() }}

    </div>

</div>

</body>

</html>