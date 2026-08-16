@extends('layouts.app')
@section('title','Tambah Sekolah')
@section('content')
<div class="page-head"><div><h1>Tambah Sekolah</h1><p>Sekolah dan akun Admin Sekolah dibuat dalam satu proses.</p></div></div>
<form method="POST" action="{{ route('website-admin.schools.store') }}">@csrf
<div class="card"><h2 class="section-title">Informasi Sekolah</h2><div class="form-grid">
<div class="field"><label>Kode Sekolah *</label><input name="code" value="{{ old('code') }}" required placeholder="SMAN01"></div><div class="field"><label>Nama Sekolah *</label><input name="name" value="{{ old('name') }}" required></div>
<div class="field"><label>Email Sekolah</label><input type="email" name="email" value="{{ old('email') }}"></div><div class="field"><label>Telepon</label><input name="phone" value="{{ old('phone') }}"></div><div class="field full"><label>Alamat</label><textarea name="address">{{ old('address') }}</textarea></div></div></div>
<div class="card"><h2 class="section-title">Admin Sekolah Pertama</h2><div class="form-grid">
<div class="field"><label>Nama Admin *</label><input name="admin_name" value="{{ old('admin_name') }}" required></div><div class="field"><label>Email Login *</label><input type="email" name="admin_email" value="{{ old('admin_email') }}" required></div><div class="field"><label>Password *</label><input type="password" name="admin_password" required></div><div class="field"><label>Konfirmasi Password *</label><input type="password" name="admin_password_confirmation" required></div></div></div>
<div class="actions"><a class="btn" href="{{ route('website-admin.schools.index') }}">Batal</a><button class="btn btn-primary">Simpan Sekolah</button></div></form>
@endsection
