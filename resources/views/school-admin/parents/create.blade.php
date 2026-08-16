@extends('layouts.app')
@section('title','Tambah Orang Tua')
@section('content')<div class="page-head"><div><h1>Tambah Orang Tua / Wali</h1><p>Buat akun monitoring siswa.</p></div></div><form method="POST" action="{{ route('school-admin.parents.store') }}">@csrf @php($parent=null) @include('school-admin.parents._form')<div class="actions"><a class="btn" href="{{ route('school-admin.parents.index') }}">Batal</a><button class="btn btn-primary">Simpan Akun</button></div></form>@endsection
