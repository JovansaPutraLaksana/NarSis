@extends('layouts.app')
@section('title','Tetapkan Guru Pengampu')
@section('content')<div class="page-head"><div><h1>Tetapkan Guru Pengampu</h1><p>Kelas dan semester harus berada pada tahun ajaran yang sama.</p></div></div><form method="POST" action="{{ route('school-admin.teaching-assignments.store') }}">@csrf @php($teachingAssignment=null) @include('school-admin.teaching-assignments._form')<div class="actions"><a class="btn" href="{{ route('school-admin.teaching-assignments.index') }}">Batal</a><button class="btn btn-primary">Simpan</button></div></form>@endsection
