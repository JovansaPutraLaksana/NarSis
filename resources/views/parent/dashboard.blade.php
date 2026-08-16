@extends('layouts.app')
@section('title','Anak Saya')
@section('content')
<div class="page-head"><div><h1>Anak Saya</h1><p>Pilih anak untuk melihat absensi dan tugas.</p></div></div>
<div class="grid-3">@forelse($children as $child)<div class="card"><h2 class="section-title">{{ $child->user->name }}</h2><p class="muted">NIS: {{ $child->nis?:'-' }} · {{ $child->pivot->relationship }}</p><a class="btn btn-primary" href="{{ route('parent.children.show',$child) }}">Lihat Aktivitas</a></div>@empty<div class="card empty">Belum ada anak yang terhubung dengan akun ini. Hubungi Admin Sekolah.</div>@endforelse</div>
@endsection
