@extends('layouts.app')
@section('title','Dashboard Guru')
@section('content')
<div class="page-head"><div><h1>Dashboard Guru</h1><p>{{ $profile?->user?->name }} · {{ $activeSemester ? $activeSemester->name : 'Belum ada semester aktif' }}</p></div></div>
<div class="stats"><div class="stat"><div class="stat-label">Materi Semester Aktif</div><div class="stat-value">{{ $stats['materials'] }}</div></div><div class="stat"><div class="stat-label">Tugas Semester Aktif</div><div class="stat-value">{{ $stats['assignments'] }}</div></div></div>
<div class="card"><h2 class="section-title">Jadwal Hari Ini · {{ now()->translatedFormat('l, d F Y') }}</h2>
@forelse($todaySchedules as $schedule)<div class="card schedule-card"><strong>{{ substr($schedule->start_time,0,5) }}–{{ substr($schedule->end_time,0,5) }} · {{ $schedule->teachingAssignment->subject->name }}</strong><div class="muted">{{ $schedule->teachingAssignment->schoolClass->name }} · {{ $schedule->room ?: 'Ruang -' }}</div></div>@empty<div class="empty">Tidak ada jadwal mengajar hari ini.</div>@endforelse
</div>
@endsection
