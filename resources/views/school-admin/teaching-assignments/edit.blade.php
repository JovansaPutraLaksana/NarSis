@extends('layouts.app')
@section('title','Edit Guru Pengampu')
@section('content')<div class="page-head"><div><h1>Edit Guru Pengampu</h1></div></div><form method="POST" action="{{ route('school-admin.teaching-assignments.update',$teachingAssignment) }}">@csrf @method('PUT') @include('school-admin.teaching-assignments._form')<div class="actions"><a class="btn" href="{{ route('school-admin.teaching-assignments.index') }}">Batal</a><button class="btn btn-primary">Simpan Perubahan</button></div></form>@endsection
