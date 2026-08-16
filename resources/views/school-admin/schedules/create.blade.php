@extends('layouts.app')
@section('title','Tambah Jadwal')
@section('content')<div class="page-head"><div><h1>Tambah Jadwal Pelajaran</h1></div></div><form method="POST" action="{{ route('school-admin.schedules.store') }}">@csrf @php($schedule=null) @include('school-admin.schedules._form')<div class="actions"><a class="btn" href="{{ route('school-admin.schedules.index') }}">Batal</a><button class="btn btn-primary">Simpan Jadwal</button></div></form>@endsection
