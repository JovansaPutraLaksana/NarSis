@extends('layouts.app')
@section('title','Edit Sekolah')
@section('content')
<div class="page-head"><div><h1>Edit Sekolah</h1><p>{{ $school->name }}</p></div></div>
<form method="POST" action="{{ route('website-admin.schools.update',$school) }}">@csrf @method('PUT')<div class="card"><div class="form-grid">
<div class="field"><label>Kode Sekolah *</label><input name="code" value="{{ old('code',$school->code) }}" required></div><div class="field"><label>Nama Sekolah *</label><input name="name" value="{{ old('name',$school->name) }}" required></div>
<div class="field"><label>Email</label><input type="email" name="email" value="{{ old('email',$school->email) }}"></div><div class="field"><label>Telepon</label><input name="phone" value="{{ old('phone',$school->phone) }}"></div><div class="field full"><label>Alamat</label><textarea name="address">{{ old('address',$school->address) }}</textarea></div></div></div><div class="actions"><a class="btn" href="{{ route('website-admin.schools.show',$school) }}">Batal</a><button class="btn btn-primary">Simpan Perubahan</button></div></form>
@endsection
