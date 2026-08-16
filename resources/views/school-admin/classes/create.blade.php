@extends('layouts.app')
@section('title','Tambah Kelas')
@section('content')<div class="page-head"><div><h1>Tambah Kelas</h1><p>Kelas dibuat untuk satu tahun ajaran.</p></div></div><form method="POST" action="{{ route('school-admin.classes.store') }}">@csrf @php($class=null) @include('school-admin.classes._form')<div class="actions"><a class="btn" href="{{ route('school-admin.classes.index') }}">Batal</a><button class="btn btn-primary">Simpan Kelas</button></div></form>@endsection
