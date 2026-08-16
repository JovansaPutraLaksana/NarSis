@extends('layouts.app')
@section('title','Edit Guru')
@section('content')<div class="page-head"><div><h1>Edit Guru</h1><p>{{ $teacher->user->name }}</p></div></div><form method="POST" action="{{ route('school-admin.teachers.update',$teacher) }}">@csrf @method('PUT') @include('school-admin.teachers._form')<div class="actions"><a class="btn" href="{{ route('school-admin.teachers.index') }}">Batal</a><button class="btn btn-primary">Simpan Perubahan</button></div></form>@endsection
