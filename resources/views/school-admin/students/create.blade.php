@extends('layouts.app')
@section('title','Tambah Siswa')
@section('content')<div class="page-head"><div><h1>Tambah Siswa</h1><p>Buat akun siswa dan data identitas dasarnya.</p></div></div><form method="POST" action="{{ route('school-admin.students.store') }}">@csrf @php($student=null) @include('school-admin.students._form')<div class="actions"><a class="btn" href="{{ route('school-admin.students.index') }}">Batal</a><button class="btn btn-primary">Simpan Siswa</button></div></form>@endsection
