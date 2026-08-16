@extends('layouts.app')
@section('title','Materi')
@section('content')
<div class="page-head"><div><h1>Materi Pembelajaran</h1><p>{{ $semester ? 'Semester '.$semester->name : 'Belum ada semester aktif' }}</p></div></div>
<div class="table-wrap"><table><thead><tr><th>Materi</th><th>Mata Pelajaran</th><th>Guru</th><th>Publikasi</th><th>File</th></tr></thead><tbody>@forelse($materials as $material)<tr><td><strong>{{ $material->title }}</strong><div class="muted">{{ $material->description }}</div></td><td>{{ $material->teachingAssignment->subject->name }}</td><td>{{ $material->teacher->user->name }}</td><td>{{ $material->published_at?->format('d M Y H:i') }}</td><td>@if($material->file_path)<a class="btn btn-sm" href="{{ route('files.materials',$material) }}">Download</a>@else-@endif</td></tr>@empty<tr><td colspan="5" class="empty">Belum ada materi.</td></tr>@endforelse</tbody></table></div>@if($materials instanceof \Illuminate\Contracts\Pagination\Paginator)<div class="pagination">{{ $materials->links() }}</div>@endif
@endsection
