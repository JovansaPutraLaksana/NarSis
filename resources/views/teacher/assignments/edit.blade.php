@extends('layouts.app')
@section('title','Edit Tugas')
@section('content')
<div class="page-head"><div><h1>Edit Tugas</h1><p>{{ $assignment->teachingAssignment->subject->name }} · {{ $assignment->teachingAssignment->schoolClass->name }}</p></div></div>
<form method="POST" enctype="multipart/form-data" action="{{ route('teacher.assignments.update',$assignment) }}">@csrf @method('PUT')
<div class="card"><div class="form-grid">
<div class="field full"><label>Judul Tugas *</label><input name="title" value="{{ old('title',$assignment->title) }}" required></div>
<div class="field full"><label>Instruksi</label><textarea name="description">{{ old('description',$assignment->description) }}</textarea></div>
<div class="field"><label>Tenggat *</label><input type="datetime-local" name="due_at" value="{{ old('due_at',$assignment->due_at?->format('Y-m-d\\TH:i')) }}" required></div>
<div class="field"><label>Nilai Maksimal *</label><input type="number" step="0.01" name="max_score" value="{{ old('max_score',$assignment->max_score) }}" required></div>
<div class="field full"><label>Ganti Lampiran Soal</label><input type="file" name="file"><small>Maks. {{ number_format(config('narsis.upload_max_kb', 4096) / 1024, 1) }} MB.</small>@if($assignment->file_path)<div style="margin-top:8px"><a href="{{ route('files.assignments',$assignment) }}">{{ $assignment->file_name }}</a> · <label><input type="checkbox" name="remove_file" value="1"> Hapus lampiran lama</label></div>@endif</div>
</div></div>
<div class="actions"><a class="btn" href="{{ route('teacher.assignments.index') }}">Batal</a><button class="btn btn-primary">Simpan Perubahan</button></div>
</form>
@endsection
