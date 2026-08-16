@extends('layouts.app')
@section('title','Sekolah')
@section('content')
<div class="page-head"><div><h1>Daftar Sekolah</h1><p>Kelola tenant sekolah dan status penggunaannya.</p></div><a class="btn btn-primary" href="{{ route('website-admin.schools.create') }}">+ Tambah Sekolah</a></div>
<form class="search" method="GET"><input name="q" value="{{ request('q') }}" placeholder="Cari kode / nama sekolah"><button class="btn">Cari</button></form>
<div class="table-wrap"><table><thead><tr><th>Kode</th><th>Nama Sekolah</th><th>Email</th><th>Pengguna</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
@forelse($schools as $school)<tr><td>{{ $school->code }}</td><td>{{ $school->name }}</td><td>{{ $school->email ?: '-' }}</td><td>{{ $school->users_count }}</td><td><span class="badge {{ $school->is_active?'badge-success':'badge-danger' }}">{{ $school->is_active?'Aktif':'Nonaktif' }}</span></td><td><div class="actions"><a class="btn btn-sm" href="{{ route('website-admin.schools.show',$school) }}">Detail</a><a class="btn btn-sm" href="{{ route('website-admin.schools.edit',$school) }}">Edit</a><form method="POST" action="{{ route('website-admin.schools.status',$school) }}">@csrf @method('PATCH')<button class="btn btn-sm {{ $school->is_active?'btn-danger':'btn-success' }}">{{ $school->is_active?'Nonaktifkan':'Aktifkan' }}</button></form></div></td></tr>
@empty<tr><td colspan="6" class="empty">Belum ada sekolah.</td></tr>@endforelse
</tbody></table></div><div class="pagination">{{ $schools->links() }}</div>
@endsection
