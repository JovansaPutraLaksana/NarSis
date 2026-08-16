@extends('layouts.app')
@section('title','Absensi Saya')
@section('content')
<div class="page-head"><div><h1>Absensi Saya</h1><p>Riwayat kehadiran berdasarkan mata pelajaran.</p></div></div>
<div class="stats">@foreach(\App\Models\AttendanceRecord::STATUSES as $key=>$label)<div class="stat"><div class="stat-label">{{ $label }}</div><div class="stat-value">{{ $summary[$key] ?? 0 }}</div></div>@endforeach</div>
<div class="table-wrap"><table><thead><tr><th>Tanggal</th><th>Mata Pelajaran</th><th>Kelas</th><th>Status</th><th>Catatan</th></tr></thead><tbody>@forelse($records as $record)<tr><td>{{ $record->session->attendance_date->format('d M Y') }}</td><td>{{ $record->session->schedule->teachingAssignment->subject->name }}</td><td>{{ $record->session->schedule->teachingAssignment->schoolClass->name }}</td><td><span class="badge {{ $record->status==='present'?'badge-success':($record->status==='absent'?'badge-danger':'badge-warning') }}">{{ \App\Models\AttendanceRecord::STATUSES[$record->status] ?? $record->status }}</span></td><td>{{ $record->note?:'-' }}</td></tr>@empty<tr><td colspan="5" class="empty">Belum ada data absensi.</td></tr>@endforelse</tbody></table></div><div class="pagination">{{ $records->links() }}</div>
@endsection
