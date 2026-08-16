@extends('layouts.app')
@section('title','Siswa Kelas')
@section('content')
<div class="page-head"><div><h1>Siswa {{ $class->name }}</h1><p>{{ $class->academicYear->name }} · centang siswa yang ditempatkan pada kelas ini.</p></div></div>
<div class="alert alert-success" style="background:#eff6ff;color:#1e40af;border-color:#bfdbfe">Jika siswa sudah berada di kelas lain pada tahun ajaran yang sama, penyimpanan akan memindahkan siswa tersebut ke kelas ini.</div>
<form method="POST" action="{{ route('school-admin.classes.students.sync',$class) }}">@csrf @method('PUT')<div class="card"><div class="checkbox-list">@forelse($students as $student)<label class="checkbox-item"><input type="checkbox" name="student_ids[]" value="{{ $student->id }}" @checked(in_array($student->id,$selectedIds,true))> <strong>{{ $student->user->name }}</strong><div class="muted">{{ $student->nis?:'NIS -' }}</div></label>@empty<div class="muted">Belum ada siswa aktif.</div>@endforelse</div></div><div class="actions"><a class="btn" href="{{ route('school-admin.classes.index') }}">Kembali</a><button class="btn btn-primary">Simpan Daftar Siswa</button></div></form>
@endsection
