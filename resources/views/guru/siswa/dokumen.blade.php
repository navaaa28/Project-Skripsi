@extends('layouts.guru-dashboard')

@section('title', 'Dokumen Siswa')

@section('content')
<style>
    .panel { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; margin-bottom: 12px; }
    .breadcrumb { font-size: 12px; color: #6b7280; margin-bottom: 10px; }
    .student-box { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
    .avatar { width: 48px; height: 48px; border: 1px solid #e5e7eb; border-radius: 8px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; color: #9ca3af; }
    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    th, td { padding: 10px 8px; border-bottom: 1px solid #e5e7eb; text-align: left; }
    th { background: #f8fafc; font-weight: 600; color: #374151; }
    .badge { padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 600; }
    .badge-pdf { background: #fee2e2; color: #dc2626; }
    .badge-image { background: #dbeafe; color: #2563eb; }
    .btn-sm { padding: 4px 12px; border-radius: 6px; font-size: 12px; text-decoration: none; }
    .btn-primary { background: #2563eb; color: #fff; }
    .btn-back { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }
    .empty-state { text-align: center; padding: 40px 16px; color: #9ca3af; }
    .file-size { color: #9ca3af; font-size: 11px; }
</style>

<div class="panel">
    <p class="breadcrumb">
        <a href="{{ route('guru.siswa.index') }}" style="color:#2563eb; text-decoration:none;">Siswa</a>
        &rsaquo;
        <a href="{{ route('guru.siswa.show', $siswa) }}" style="color:#2563eb; text-decoration:none;">{{ $siswa->nama_siswa }}</a>
        &rsaquo; Dokumen
    </p>

    <div class="student-box">
        <div class="avatar"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
        <div>
            <div style="font-weight:700;">{{ $siswa->nama_siswa }}</div>
            <div style="font-size:12px;color:#6b7280;">{{ $siswa->kelas?->nama_kelas ?? '-' }} &bull; NISN: {{ $siswa->nisn ?? '-' }}</div>
        </div>
    </div>

    @if($dokumen->isEmpty())
        <div class="empty-state">
            <svg width="48" height="48" fill="none" stroke="#d1d5db" stroke-width="1.5" style="margin:0 auto 8px;"><path d="M9 12h6l3-3h6l3 3h6v24H9z"/><path d="M9 24h30"/></svg>
            <p style="font-weight:600;color:#6b7280;">Belum ada dokumen yang diupload</p>
            <p style="font-size:12px;">Siswa belum mengupload dokumen apapun melalui aplikasi mobile.</p>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis Dokumen</th>
                    <th>Nama File</th>
                    <th>Tipe</th>
                    <th>Ukuran</th>
                    <th>Tanggal Upload</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dokumen as $i => $doc)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $doc->label }}</strong></td>
                    <td>{{ $doc->nama_file }}</td>
                    <td>
                        @if(str_contains($doc->mime_type, 'pdf'))
                            <span class="badge badge-pdf">PDF</span>
                        @else
                            <span class="badge badge-image">Gambar</span>
                        @endif
                    </td>
                    <td class="file-size">{{ number_format($doc->size / 1024, 0) }} KB</td>
                    <td>{{ $doc->created_at->format('d M Y H:i') }}</td>
                    <td>
                        <a href="{{ route('guru.siswa.dokumen.download', [$siswa, $doc]) }}" class="btn-sm btn-primary">
                            ⬇ Download
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<div style="margin-top:8px;">
    <a href="{{ route('guru.siswa.show', $siswa) }}" class="btn-sm btn-back">← Kembali ke Detail Siswa</a>
</div>
@endsection
