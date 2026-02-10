@extends('layouts.admin-dashboard')

@section('title', 'Edit Mapel')

@section('content')
<style>
    .page-title { font-size: 16px; font-weight: 700; margin-bottom: 12px; }
    .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; max-width: 600px; }
    .field { margin-bottom: 12px; }
    .label { display: block; font-size: 12px; color: #6b7280; margin-bottom: 6px; }
    .input { width: 100%; border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px 10px; font-size: 12px; }
    .btn { background: #2563eb; color: #fff; border: none; padding: 8px 12px; border-radius: 6px; font-size: 12px; cursor: pointer; }
    .btn-secondary { background: #e5e7eb; color: #111827; text-decoration: none; margin-left: 6px; }
</style>

<div class="page-title">Edit Mapel</div>
<div class="card">
    <form method="POST" action="{{ route('admin.mapel.update', $mapel) }}">
        @csrf
        @method('PUT')
        <div class="field">
            <label class="label">Nama Mapel</label>
            <input name="nama_mapel" value="{{ $mapel->nama_mapel }}" class="input" required>
        </div>
        <div class="field">
            <label class="label">KKM</label>
            <input type="number" name="kkm" value="{{ $mapel->kkm }}" class="input" required>
        </div>
        <button class="btn" type="submit">Update</button>
        <a href="{{ route('admin.mapel.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
