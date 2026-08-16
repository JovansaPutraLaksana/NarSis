@extends('layouts.app')
@section('title','Edit Kelas')
@section('content')<div class="page-head"><div><h1>Edit Kelas</h1><p>{{ $class->name }}</p></div></div><form method="POST" action="{{ route('school-admin.classes.update',$class) }}">@csrf @method('PUT') @include('school-admin.classes._form')<div class="actions"><a class="btn" href="{{ route('school-admin.classes.index') }}">Batal</a><button class="btn btn-primary">Simpan Perubahan</button></div></form>@endsection
