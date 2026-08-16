@extends('layouts.app')
@section('title','Dashboard Platform')
@section('content')
<div class="page-head"><div><h1>Dashboard Platform</h1><p>Ringkasan seluruh sekolah yang menggunakan NarSis.</p></div><a class="btn btn-primary" href="{{ route('website-admin.schools.create') }}">+ Tambah Sekolah</a></div>
<div class="stats">
    <div class="stat"><div class="stat-label">Total Sekolah</div><div class="stat-value">{{ $stats['schools'] }}</div></div>
    <div class="stat"><div class="stat-label">Sekolah Aktif</div><div class="stat-value">{{ $stats['active_schools'] }}</div></div>
    <div class="stat"><div class="stat-label">Admin Sekolah</div><div class="stat-value">{{ $stats['school_admins'] }}</div></div>
    <div class="stat"><div class="stat-label">Pengguna Sekolah</div><div class="stat-value">{{ $stats['users'] }}</div></div>
</div>
<div class="card"><h2 class="section-title">Sekolah Terbaru</h2>
@if($recentSchools->isEmpty())<div class="empty">Belum ada sekolah.</div>@else
<div class="table-wrap"><table><thead><tr><th>Kode</th><th>Sekolah</th><th>Status</th><th></th></tr></thead><tbody>@foreach($recentSchools as $school)<tr><td>{{ $school->code }}</td><td>{{ $school->name }}</td><td><span class="badge {{ $school->is_active?'badge-success':'badge-danger' }}">{{ $school->is_active?'Aktif':'Nonaktif' }}</span></td><td><a href="{{ route('website-admin.schools.show',$school) }}">Detail</a></td></tr>@endforeach</tbody></table></div>@endif
</div>
@endsection
