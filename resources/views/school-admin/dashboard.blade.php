@extends('layouts.app')
@section('title','Dashboard Sekolah')
@section('content')
<div class="page-head"><div><h1>Dashboard Sekolah</h1><p>{{ auth()->user()->school->name }}</p></div></div>
<div class="stats">
<div class="stat"><div class="stat-label">Guru Aktif</div><div class="stat-value">{{ $stats['teachers'] }}</div></div>
<div class="stat"><div class="stat-label">Siswa Aktif</div><div class="stat-value">{{ $stats['students'] }}</div></div>
<div class="stat"><div class="stat-label">Kelas Aktif</div><div class="stat-value">{{ $stats['classes'] }}</div></div>
<div class="stat"><div class="stat-label">Mata Pelajaran</div><div class="stat-value">{{ $stats['subjects'] }}</div></div>
</div>
<div class="grid-2"><div class="card"><h2 class="section-title">Periode Aktif</h2>@if($activeYear)<p><strong>{{ $activeYear->name }}</strong></p>@php($activeSemester=$activeYear->semesters->firstWhere('is_active',true))<p class="muted">Semester: {{ $activeSemester?->name ?? 'Belum dipilih' }}</p>@else<p class="muted">Belum ada tahun ajaran aktif.</p><a class="btn btn-primary" href="{{ route('school-admin.academic-years.index') }}">Atur Tahun Ajaran</a>@endif</div>
<div class="card"><h2 class="section-title">Orang Tua / Wali</h2><div class="stat-value">{{ $stats['parents'] }}</div><p class="muted">akun aktif yang dapat memonitor siswa.</p></div></div>
@endsection
