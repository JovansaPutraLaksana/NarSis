@extends('layouts.app')
@section('title','Dashboard Siswa')
@section('content')
<div class="page-head"><div><h1>Dashboard Siswa</h1><p>{{ $enrollment?->schoolClass?->name ?? 'Belum ditempatkan di kelas aktif' }} @if($activeSemester)· {{ $activeSemester->academicYear->name }} / {{ $activeSemester->name }}@endif</p></div></div>
<div class="grid-2"><div class="card"><h2 class="section-title">Jadwal Hari Ini</h2>@forelse($todaySchedules as $schedule)<div class="card schedule-card"><strong>{{ substr($schedule->start_time,0,5) }}–{{ substr($schedule->end_time,0,5) }} · {{ $schedule->teachingAssignment->subject->name }}</strong><div class="muted">{{ $schedule->teachingAssignment->teacher->user->name }} · {{ $schedule->room?:'Ruang -' }}</div></div>@empty<div class="empty">Tidak ada jadwal hari ini.</div>@endforelse</div>
<div class="card"><h2 class="section-title">Tugas Mendatang</h2>@forelse($upcomingAssignments as $assignment)<div style="padding:10px 0;border-bottom:1px solid var(--line)"><a href="{{ route('student.assignments.show',$assignment) }}"><strong>{{ $assignment->title }}</strong></a><div class="muted">{{ $assignment->teachingAssignment->subject->name }} · {{ $assignment->due_at->format('d M Y H:i') }}</div></div>@empty<div class="empty">Tidak ada tugas aktif.</div>@endforelse</div></div>
@endsection
