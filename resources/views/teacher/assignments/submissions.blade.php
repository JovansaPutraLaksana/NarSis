@extends('layouts.app')
@section('title','Pengumpulan Tugas')
@section('content')
<div class="page-head"><div><h1>{{ $assignment->title }}</h1><p>{{ $assignment->teachingAssignment->subject->name }} · {{ $assignment->teachingAssignment->schoolClass->name }} · Maks. {{ $assignment->max_score }}</p></div></div>
<div class="table-wrap"><table><thead><tr><th>Siswa</th><th>Status / Pengumpulan</th><th>Jawaban</th><th>Nilai / Feedback</th></tr></thead><tbody>
@forelse($students as $enrollment) @php($student=$enrollment->student) @php($submission=$submissions->get($student->id))
<tr><td><strong>{{ $student->user->name }}</strong><div class="muted">{{ $student->nis ?: '-' }}</div></td>
<td>@if($submission)<span class="badge badge-success">Dikumpulkan</span><div class="muted">{{ $submission->submitted_at->format('d M Y H:i') }}</div>@else<span class="badge badge-warning">Belum dikumpulkan</span>@endif</td>
<td>@if($submission) @if($submission->file_path)<a href="{{ route('files.submissions',$submission) }}">{{ $submission->file_name }}</a><br>@endif<div class="muted">{{ $submission->note }}</div>@else-@endif</td>
<td>@if($submission)<form method="POST" action="{{ route('teacher.assignments.grade',[$assignment,$submission]) }}">@csrf @method('PATCH')<div class="field" style="margin-bottom:8px"><input type="number" step="0.01" min="0" max="{{ $assignment->max_score }}" name="score" value="{{ old('score',$submission->score) }}" placeholder="Nilai" required></div><div class="field" style="margin-bottom:8px"><textarea name="feedback" placeholder="Feedback">{{ old('feedback',$submission->feedback) }}</textarea></div><button class="btn btn-sm btn-primary">Simpan Nilai</button></form>@else-@endif</td></tr>
@empty<tr><td colspan="4" class="empty">Belum ada siswa di kelas ini.</td></tr>@endforelse
</tbody></table></div><div style="margin-top:16px"><a class="btn" href="{{ route('teacher.assignments.index') }}">Kembali</a></div>
@endsection
