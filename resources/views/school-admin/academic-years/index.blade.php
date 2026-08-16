@extends('layouts.app')
@section('title','Tahun Ajaran')
@section('content')
<div class="page-head"><div><h1>Tahun Ajaran & Semester</h1><p>Tetapkan satu semester aktif sebagai dasar kelas, jadwal, dan aktivitas belajar.</p></div><a class="btn btn-primary" href="{{ route('school-admin.academic-years.create') }}">+ Tahun Ajaran</a></div>
@forelse($academicYears as $year)<div class="card"><div class="page-head" style="margin-bottom:12px"><div><h1 style="font-size:20px">{{ $year->name }} @if($year->is_active)<span class="badge badge-success">Aktif</span>@endif</h1><p>{{ $year->start_date->format('d M Y') }} – {{ $year->end_date->format('d M Y') }}</p></div><a class="btn btn-sm" href="{{ route('school-admin.academic-years.edit',$year) }}">Edit Periode</a></div>
<div class="table-wrap"><table><thead><tr><th>Semester</th><th>Periode</th><th>Status</th><th>Aksi</th></tr></thead><tbody>@foreach($year->semesters as $semester)<tr><td>{{ $semester->name }}</td><td>{{ $semester->start_date->format('d M Y') }} – {{ $semester->end_date->format('d M Y') }}</td><td><span class="badge {{ $semester->is_active?'badge-success':'badge-info' }}">{{ $semester->is_active?'Aktif':'Tidak Aktif' }}</span></td><td>@unless($semester->is_active)<form method="POST" action="{{ route('school-admin.academic-years.semesters.activate',[$year,$semester]) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-primary">Aktifkan Semester</button></form>@endunless</td></tr>@endforeach</tbody></table></div></div>
@empty<div class="card empty">Belum ada tahun ajaran.</div>@endforelse
@endsection
