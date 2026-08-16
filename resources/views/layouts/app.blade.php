<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'NarSis') - NarSis</title>
    <link rel="stylesheet" href="{{ asset('css/narsis.css') }}">
</head>
<body>
@php($role = auth()->user()->role->value)
<div class="app">
    <aside class="sidebar">
        <div class="brand">NarSis</div>
        <div class="brand-sub">Sistem Informasi Sekolah</div>
        <nav class="nav">
            @if($role === 'website_admin')
                <div class="nav-title">Platform</div>
                <a class="{{ request()->routeIs('website-admin.dashboard') ? 'active' : '' }}" href="{{ route('website-admin.dashboard') }}">Dashboard</a>
                <a class="{{ request()->routeIs('website-admin.schools.*') ? 'active' : '' }}" href="{{ route('website-admin.schools.index') }}">Sekolah</a>
            @elseif($role === 'school_admin')
                <div class="nav-title">Sekolah</div>
                <a class="{{ request()->routeIs('school-admin.dashboard') ? 'active' : '' }}" href="{{ route('school-admin.dashboard') }}">Dashboard</a>
                <a class="{{ request()->routeIs('school-admin.academic-years.*') ? 'active' : '' }}" href="{{ route('school-admin.academic-years.index') }}">Tahun Ajaran</a>
                <div class="nav-title">Pengguna</div>
                <a class="{{ request()->routeIs('school-admin.teachers.*') ? 'active' : '' }}" href="{{ route('school-admin.teachers.index') }}">Guru</a>
                <a class="{{ request()->routeIs('school-admin.students.*') ? 'active' : '' }}" href="{{ route('school-admin.students.index') }}">Siswa</a>
                <a class="{{ request()->routeIs('school-admin.parents.*') ? 'active' : '' }}" href="{{ route('school-admin.parents.index') }}">Orang Tua / Wali</a>
                <div class="nav-title">Akademik</div>
                <a class="{{ request()->routeIs('school-admin.classes.*') ? 'active' : '' }}" href="{{ route('school-admin.classes.index') }}">Kelas</a>
                <a class="{{ request()->routeIs('school-admin.subjects.*') ? 'active' : '' }}" href="{{ route('school-admin.subjects.index') }}">Mata Pelajaran</a>
                <a class="{{ request()->routeIs('school-admin.teaching-assignments.*') ? 'active' : '' }}" href="{{ route('school-admin.teaching-assignments.index') }}">Guru Pengampu</a>
                <a class="{{ request()->routeIs('school-admin.schedules.*') ? 'active' : '' }}" href="{{ route('school-admin.schedules.index') }}">Jadwal Pelajaran</a>
            @elseif($role === 'teacher')
                <div class="nav-title">Guru</div>
                <a class="{{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}" href="{{ route('teacher.dashboard') }}">Dashboard</a>
                <a class="{{ request()->routeIs('teacher.attendance.*') ? 'active' : '' }}" href="{{ route('teacher.attendance.index') }}">Absensi</a>
                <a class="{{ request()->routeIs('teacher.materials.*') ? 'active' : '' }}" href="{{ route('teacher.materials.index') }}">Materi</a>
                <a class="{{ request()->routeIs('teacher.assignments.*') ? 'active' : '' }}" href="{{ route('teacher.assignments.index') }}">Tugas & Penilaian</a>
            @elseif($role === 'student')
                <div class="nav-title">Siswa</div>
                <a class="{{ request()->routeIs('student.dashboard') ? 'active' : '' }}" href="{{ route('student.dashboard') }}">Dashboard</a>
                <a class="{{ request()->routeIs('student.attendance.*') ? 'active' : '' }}" href="{{ route('student.attendance.index') }}">Absensi Saya</a>
                <a class="{{ request()->routeIs('student.materials.*') ? 'active' : '' }}" href="{{ route('student.materials.index') }}">Materi</a>
                <a class="{{ request()->routeIs('student.assignments.*') ? 'active' : '' }}" href="{{ route('student.assignments.index') }}">Tugas</a>
            @elseif($role === 'parent')
                <div class="nav-title">Orang Tua</div>
                <a class="{{ request()->routeIs('parent.dashboard') ? 'active' : '' }}" href="{{ route('parent.dashboard') }}">Anak Saya</a>
            @endif
            <div class="nav-title">Akun</div>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Keluar</button></form>
        </nav>
    </aside>
    <main class="main">
        <header class="topbar">
            <div class="school">{{ auth()->user()->school?->name ?? 'NarSis Platform' }}</div>
            <div class="user">{{ auth()->user()->name }} · {{ auth()->user()->role->label() }}</div>
        </header>
        <div class="content">
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if($errors->any())
                <div class="alert alert-error"><strong>Periksa kembali data:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            @yield('content')
        </div>
    </main>
</div>
</body>
</html>
