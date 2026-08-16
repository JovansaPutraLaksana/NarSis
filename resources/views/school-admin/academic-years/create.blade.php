@extends('layouts.app')
@section('title','Tambah Tahun Ajaran')
@section('content')
<div class="page-head"><div><h1>Tambah Tahun Ajaran</h1><p>Tentukan periode tahun ajaran beserta tanggal semester Ganjil dan Genap.</p></div></div>
<form method="POST" action="{{ route('school-admin.academic-years.store') }}">@csrf
<div class="card"><div class="form-grid"><div class="field"><label>Nama Tahun Ajaran *</label><input name="name" value="{{ old('name') }}" placeholder="2026/2027" required></div><div></div><div class="field"><label>Mulai Tahun Ajaran *</label><input type="date" name="start_date" value="{{ old('start_date') }}" required></div><div class="field"><label>Selesai Tahun Ajaran *</label><input type="date" name="end_date" value="{{ old('end_date') }}" required></div></div></div>
<div class="grid-2"><div class="card"><h2 class="section-title">Semester Ganjil</h2><div class="field"><label>Mulai *</label><input type="date" name="odd_start_date" value="{{ old('odd_start_date') }}" required></div><div class="field"><label>Selesai *</label><input type="date" name="odd_end_date" value="{{ old('odd_end_date') }}" required></div></div><div class="card"><h2 class="section-title">Semester Genap</h2><div class="field"><label>Mulai *</label><input type="date" name="even_start_date" value="{{ old('even_start_date') }}" required></div><div class="field"><label>Selesai *</label><input type="date" name="even_end_date" value="{{ old('even_end_date') }}" required></div></div></div>
<div class="actions"><a class="btn" href="{{ route('school-admin.academic-years.index') }}">Batal</a><button class="btn btn-primary">Simpan Tahun Ajaran</button></div></form>
@endsection
