@extends('layouts.app')
@section('title','Tambah Mata Pelajaran')
@section('content')<div class="page-head"><div><h1>Tambah Mata Pelajaran</h1></div></div><form method="POST" action="{{ route('school-admin.subjects.store') }}">@csrf @php($subject=null) @include('school-admin.subjects._form')<div class="actions"><a class="btn" href="{{ route('school-admin.subjects.index') }}">Batal</a><button class="btn btn-primary">Simpan</button></div></form>@endsection
