@extends('layouts.admin-dashboard')

@section('title', 'Edit Tahun Ajaran')

@section('content')
<style>
    .page-title { font-size: 16px; font-weight: 700; margin-bottom: 12px; }
    .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; max-width: 720px; }
    .field { margin-bottom: 12px; }
    .label { display: block; font-size: 12px; color: #6b7280; margin-bottom: 6px; }
    .select { width: 100%; border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px 10px; font-size: 12px; }
    .hint { font-size: 11px; color: #94a3b8; margin-top: 4px; }
    .btn { background: #2563eb; color: #fff; border: none; padding: 8px 12px; border-radius: 6px; font-size: 12px; cursor: pointer; }
    .btn-secondary { background: #e5e7eb; color: #111827; text-decoration: none; margin-left: 6px; }
</style>

<div class="page-title">Edit Tahun Ajaran</div>
<div class="card">
    <form method="POST" action="{{ route('admin.tahun-ajaran.update', $tahunAjaran) }}">
        @csrf @method('PUT')
        <div class="field">
            <label class="label">Tahun Ajaran</label>
            <select name="nama_tahun_ajaran" class="select" required>
                <option value="">-- Pilih Tahun Ajaran --</option>
                @php
                    $currentYear = (int) date('Y');
                    $options = [];
                    for ($y = 2020; $y <= $currentYear + 5; $y++) {
                        $options[] = $y . '/' . ($y + 1);
                    }
                @endphp
                @foreach ($options as $opt)
                    <option value="{{ $opt }}" @selected(old('nama_tahun_ajaran', $tahunAjaran->nama_tahun_ajaran) === $opt)>{{ $opt }}</option>
                @endforeach
            </select>
            <div class="hint">Format otomatis: YYYY/YYYY.</div>
        </div>
        <button class="btn" type="submit">Simpan</button>
        <a href="{{ route('admin.tahun-ajaran.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
