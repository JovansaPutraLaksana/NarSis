@extends('layouts.app')
@section('title','Absensi '.$assignment->subject->name)
@section('content')
<div class="page-head"><div><h1>Absensi {{ $assignment->subject->name }}</h1><p>{{ $assignment->schoolClass->name }} · {{ now()->format('d M Y') }} · {{ substr($schedule->start_time,0,5) }}–{{ substr($schedule->end_time,0,5) }}</p></div></div>
<form method="POST" action="{{ route('teacher.attendance.store',$schedule) }}">@csrf<div class="table-wrap"><table><thead><tr><th>Siswa</th><th>Status</th><th>Catatan</th></tr></thead><tbody>
@forelse($students as $enrollment) @php($student=$enrollment->student) @php($existing=$records->get($student->id))
<tr><td><strong>{{ $student->user->name }}</strong><div class="muted">{{ $student->nis ?: '-' }}</div></td><td><div class="attendance-status">@foreach(\App\Models\AttendanceRecord::STATUSES as $value=>$label)<label><input type="radio" name="attendance[{{ $student->id }}][status]" value="{{ $value }}" @checked(old("attendance.{$student->id}.status",$existing?->status ?? 'present')===$value)> {{ $label }}</label>@endforeach</div></td><td><input style="width:100%" name="attendance[{{ $student->id }}][note]" value="{{ old("attendance.{$student->id}.note",$existing?->note) }}" placeholder="Opsional"></td></tr>
@empty<tr><td colspan="3" class="empty">Belum ada siswa di kelas ini.</td></tr>@endforelse
</tbody></table></div><div class="actions" style="margin-top:16px"><a class="btn" href="{{ route('teacher.attendance.index') }}">Kembali</a>@if($students->isNotEmpty())<button class="btn btn-primary">Simpan Absensi</button>@endif</div></form>
@endsection
