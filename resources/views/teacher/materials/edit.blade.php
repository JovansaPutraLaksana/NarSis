@extends('layouts.app')
@section('title','Edit Materi')
@section('content')
<div class="page-head"><div><h1>Edit Materi</h1><p>Perbarui judul, kelas/mapel, deskripsi, atau lampiran materi.</p></div></div>
<form method="POST" enctype="multipart/form-data" action="{{ route('teacher.materials.update',$material) }}">@csrf @method('PUT')
<div class="card"><div class="form-grid">
<div class="field full"><label>Kelas / Mata Pelajaran *</label><select name="teaching_assignment_id" required><option value="">- Pilih -</option>@foreach($assignments as $item)<option value="{{ $item->id }}" @selected((int)old('teaching_assignment_id',$material->teaching_assignment_id)===$item->id)>{{ $item->schoolClass->name }} · {{ $item->subject->name }} · {{ $item->semester->name }}</option>@endforeach</select></div>
<div class="field full"><label>Judul Materi *</label><input name="title" value="{{ old('title',$material->title) }}" required></div>
<div class="field full"><label>Deskripsi</label><textarea name="description">{{ old('description',$material->description) }}</textarea></div>
<div class="field full"><label>Ganti File</label><input type="file" name="file"><small>Maks. {{ number_format(config('narsis.upload_max_kb', 4096) / 1024, 1) }} MB. Kosongkan jika file tidak berubah.</small>@if($material->file_path)<div style="margin-top:8px"><a href="{{ route('files.materials',$material) }}">{{ $material->file_name }}</a> · <label><input type="checkbox" name="remove_file" value="1"> Hapus lampiran lama</label></div>@endif</div>
</div></div>
<div class="actions"><a class="btn" href="{{ route('teacher.materials.index') }}">Batal</a><button class="btn btn-primary">Simpan Perubahan</button></div>
</form>
@endsection
