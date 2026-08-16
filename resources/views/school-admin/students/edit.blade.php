@extends('layouts.app')
@section('title','Edit Siswa')
@section('content')<div class="page-head"><div><h1>Edit Siswa</h1><p>{{ $student->user->name }}</p></div></div><form method="POST" action="{{ route('school-admin.students.update',$student) }}">@csrf @method('PUT') @include('school-admin.students._form')<div class="actions"><a class="btn" href="{{ route('school-admin.students.index') }}">Batal</a><button class="btn btn-primary">Simpan Perubahan</button></div></form>@endsection
