@extends('layouts.app')
@section('title','Absensi')
@section('content')
<div class="page-head"><div><h1>Absensi Hari Ini</h1><p>Absensi hanya dapat dibuka ketika jam pelajaran sedang berlangsung.</p></div></div>
@if(!$activeSemester)<div class="card empty">Belum ada semester aktif.</div>@endif
@forelse($schedules as $schedule)
@php($nowTime=now()->format('H:i:s')) @php($canOpen=$nowTime >= $schedule->start_time && $nowTime <= $schedule->end_time)
<div class="card schedule-card"><div class="page-head" style="margin-bottom:0"><div><h1 style="font-size:20px">{{ $schedule->teachingAssignment->subject->name }}</h1><p>{{ $schedule->teachingAssignment->schoolClass->name }} · {{ substr($schedule->start_time,0,5) }}–{{ substr($schedule->end_time,0,5) }}</p></div>@if($canOpen)<a class="btn btn-primary" href="{{ route('teacher.attendance.show',$schedule) }}">Buka Absensi</a>@else<span class="badge badge-info">Belum / sudah lewat jam</span>@endif</div></div>
@empty @if($activeSemester)<div class="card empty">Tidak ada jadwal mengajar hari ini.</div>@endif @endforelse
@endsection
