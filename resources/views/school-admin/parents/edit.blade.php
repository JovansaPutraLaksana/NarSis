@extends('layouts.app')
@section('title','Edit Orang Tua')
@section('content')<div class="page-head"><div><h1>Edit Orang Tua / Wali</h1><p>{{ $parent->user->name }}</p></div></div><form method="POST" action="{{ route('school-admin.parents.update',$parent) }}">@csrf @method('PUT') @include('school-admin.parents._form')<div class="actions"><a class="btn" href="{{ route('school-admin.parents.index') }}">Batal</a><button class="btn btn-primary">Simpan Perubahan</button></div></form>@endsection
