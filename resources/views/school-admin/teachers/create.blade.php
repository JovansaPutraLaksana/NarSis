@extends('layouts.app')
@section('title','Tambah Guru')
@section('content')<div class="page-head"><div><h1>Tambah Guru</h1><p>Buat akun login sekaligus profil guru.</p></div></div><form method="POST" action="{{ route('school-admin.teachers.store') }}">@csrf @php($teacher=null) @include('school-admin.teachers._form')<div class="actions"><a class="btn" href="{{ route('school-admin.teachers.index') }}">Batal</a><button class="btn btn-primary">Simpan Guru</button></div></form>@endsection
