<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Tambah Sekolah</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f5f6f8;
            margin: 0;
        }

        .container {
            max-width: 700px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input,
        textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 11px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        textarea {
            min-height: 90px;
        }

        button {
            background: #111827;
            color: white;
            border: 0;
            border-radius: 7px;
            padding: 12px 20px;
            cursor: pointer;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 7px;
        }

        hr {
            margin: 30px 0;
            border: 0;
            border-top: 1px solid #eee;
        }

    </style>

</head>


<body>

<div class="container">

    <a href="{{ route('website-admin.schools.index') }}">
        ← Kembali
    </a>


    <h1>Tambah Sekolah</h1>


    @if($errors->any())

        <div class="error">

            <ul>

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    <form
        method="POST"
        action="{{ route('website-admin.schools.store') }}"
    >

        @csrf


        <h2>Informasi Sekolah</h2>


        <div class="form-group">

            <label>
                Kode Sekolah
            </label>

            <input
                type="text"
                name="code"
                value="{{ old('code') }}"
                placeholder="Contoh: SDN001"
                required
            >

        </div>


        <div class="form-group">

            <label>
                Nama Sekolah
            </label>

            <input
                type="text"
                name="name"
                value="{{ old('name') }}"
                required
            >

        </div>


        <div class="form-group">

            <label>
                Alamat
            </label>

            <textarea
                name="address"
            >{{ old('address') }}</textarea>

        </div>


        <div class="form-group">

            <label>
                Nomor Telepon
            </label>

            <input
                type="text"
                name="phone"
                value="{{ old('phone') }}"
            >

        </div>


        <div class="form-group">

            <label>
                Email Sekolah
            </label>

            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
            >

        </div>


        <hr>


        <h2>
            Admin Sekolah
        </h2>


        <div class="form-group">

            <label>
                Nama Admin
            </label>

            <input
                type="text"
                name="admin_name"
                value="{{ old('admin_name') }}"
                required
            >

        </div>


        <div class="form-group">

            <label>
                Email Admin
            </label>

            <input
                type="email"
                name="admin_email"
                value="{{ old('admin_email') }}"
                required
            >

        </div>


        <div class="form-group">

            <label>
                Password
            </label>

            <input
                type="password"
                name="admin_password"
                required
            >

        </div>


        <div class="form-group">

            <label>
                Konfirmasi Password
            </label>

            <input
                type="password"
                name="admin_password_confirmation"
                required
            >

        </div>


        <button type="submit">
            Simpan Sekolah
        </button>

    </form>

</div>

</body>

</html>