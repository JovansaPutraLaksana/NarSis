@extends('layouts.app')
@section('title','Guru')
@section('content')
<div class="page-head"><div><h1>Master Guru</h1><p>Kelola akun dan profil guru.</p></div><a class="btn btn-primary" href="{{ route('school-admin.teachers.create') }}">+ Tambah Guru</a></div>
<form class="search"><input name="q" value="{{ request('q') }}" placeholder="Cari nama, email, NIP"><button class="btn">Cari</button></form>
<div class="table-wrap"><table><thead><tr><th>Guru</th><th>No. Pegawai</th><th>NIP</th><th>Kontak</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
@forelse($teachers as $teacher)<tr><td><strong>{{ $teacher->user->name }}</strong><div class="muted">{{ $teacher->user->email }}</div></td><td>{{ $teacher->employee_number?:'-' }}</td><td>{{ $teacher->nip?:'-' }}</td><td>{{ $teacher->phone?:'-' }}</td><td><span class="badge {{ $teacher->user->is_active?'badge-success':'badge-danger' }}">{{ $teacher->user->is_active?'Aktif':'Nonaktif' }}</span></td><td><div class="actions"><a class="btn btn-sm" href="{{ route('school-admin.teachers.edit',$teacher) }}">Edit</a><form method="POST" action="{{ route('school-admin.teachers.status',$teacher) }}">@csrf @method('PATCH')<button class="btn btn-sm {{ $teacher->user->is_active?'btn-danger':'btn-success' }}">{{ $teacher->user->is_active?'Nonaktifkan':'Aktifkan' }}</button></form></div></td></tr>
@empty<tr><td colspan="6" class="empty">Belum ada guru.</td></tr>@endforelse</tbody></table></div><div class="pagination">{{ $teachers->links() }}</div>
@endsection
