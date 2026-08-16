@extends('layouts.app')
@section('title','Guru Pengampu')
@section('content')
<div class="page-head"><div><h1>Guru Pengampu</h1><p>Tetapkan guru untuk mata pelajaran, kelas, dan semester.</p></div><a class="btn btn-primary" href="{{ route('school-admin.teaching-assignments.create') }}">+ Tetapkan Guru</a></div>
<form class="search"><select name="semester"><option value="">Semua Semester</option>@foreach($semesters as $semester)<option value="{{ $semester->id }}" @selected((int)request('semester')===$semester->id)>{{ $semester->academicYear->name }} · {{ $semester->name }}</option>@endforeach</select><button class="btn">Tampilkan</button></form>
<div class="table-wrap"><table><thead><tr><th>Semester</th><th>Kelas</th><th>Mata Pelajaran</th><th>Guru</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
@forelse($assignments as $item)<tr><td>{{ $item->semester->academicYear->name }}<div class="muted">{{ $item->semester->name }}</div></td><td>{{ $item->schoolClass->name }}</td><td>{{ $item->subject->name }}</td><td>{{ $item->teacher->user->name }}</td><td><span class="badge {{ $item->is_active?'badge-success':'badge-danger' }}">{{ $item->is_active?'Aktif':'Nonaktif' }}</span></td><td><div class="actions"><a class="btn btn-sm" href="{{ route('school-admin.teaching-assignments.edit',$item) }}">Edit</a><form method="POST" action="{{ route('school-admin.teaching-assignments.status',$item) }}">@csrf @method('PATCH')<button class="btn btn-sm">Ubah Status</button></form></div></td></tr>@empty<tr><td colspan="6" class="empty">Belum ada guru pengampu.</td></tr>@endforelse
</tbody></table></div><div class="pagination">{{ $assignments->links() }}</div>
@endsection
