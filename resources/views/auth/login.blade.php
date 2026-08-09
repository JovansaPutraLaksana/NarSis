<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Narsis</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
        }

        .container {
            max-width: 420px;
            margin: 100px auto;
            background: white;
            padding: 32px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,.08);
        }

        h1 {
            margin-bottom: 6px;
        }

        .subtitle {
            color: #666;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 6px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            box-sizing: border-box;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 7px;
        }

        button {
            width: 100%;
            padding: 12px;
            border: 0;
            border-radius: 7px;
            background: #111827;
            color: white;
            cursor: pointer;
        }

        .error {
            background: #fee2e2;
            padding: 12px;
            border-radius: 7px;
            color: #991b1b;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Narsis</h1>

    <div class="subtitle">
        Sistem Informasi Sekolah
    </div>

    @if ($errors->any())
        <div class="error">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}">

        @csrf

        <div class="form-group">
            <label>Email</label>

            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
            >
        </div>

        <div class="form-group">
            <label>Password</label>

            <input
                type="password"
                name="password"
                required
            >
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="remember">
                Ingat saya
            </label>
        </div>

        <button type="submit">
            Masuk
        </button>

    </form>

</div>

</body>
</html>