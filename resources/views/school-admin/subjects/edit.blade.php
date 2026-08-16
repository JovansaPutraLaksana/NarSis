@extends('layouts.app')
@section('title','Edit Mata Pelajaran')
@section('content')<div class="page-head"><div><h1>Edit Mata Pelajaran</h1></div></div><form method="POST" action="{{ route('school-admin.subjects.update',$subject) }}">@csrf @method('PUT') @include('school-admin.subjects._form')<div class="actions"><a class="btn" href="{{ route('school-admin.subjects.index') }}">Batal</a><button class="btn btn-primary">Simpan Perubahan</button></div></form>@endsection
