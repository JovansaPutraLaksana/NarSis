@extends('layouts.app')
@section('title','Edit Jadwal')
@section('content')<div class="page-head"><div><h1>Edit Jadwal Pelajaran</h1></div></div><form method="POST" action="{{ route('school-admin.schedules.update',$schedule) }}">@csrf @method('PUT') @include('school-admin.schedules._form')<div class="actions"><a class="btn" href="{{ route('school-admin.schedules.index') }}">Batal</a><button class="btn btn-primary">Simpan Perubahan</button></div></form>@endsection
