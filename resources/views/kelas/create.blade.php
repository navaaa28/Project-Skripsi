@extends('layouts.app')

@section('title', 'Tambah Kelas')

@section('content')
<h1 class="text-xl font-semibold mb-4">Tambah Kelas</h1>
<form method="POST" action="{{ route('admin.kelas.store') }}">
    @csrf
    <div class="mb-3">
        <label>Nama Kelas</label>
        <input name="nama_kelas" class="w-full border px-3 py-2" required>
    </div>
    <div class="mb-3">
        <label>Wali Kelas (Guru)</label>
        <select name="id_guru" class="w-full border px-3 py-2">
            <option value="">-</option>
            @foreach ($gurus as $guru)
                <option value="{{ $guru->id_user }}">{{ $guru->nama_guru }}</option>
            @endforeach
        </select>
    </div>
    <button class="border px-4 py-2">Simpan</button>
</form>
@endsection
