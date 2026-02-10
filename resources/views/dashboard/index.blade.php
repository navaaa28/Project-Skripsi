@extends('layouts.admin-dashboard')

@section('title', 'Dashboard')

@section('content')
<div class="topbar">
    <div class="title">Dashboard Overview</div>
    <div class="user">
        <span>Admin</span>
    </div>
</div>

<div class="cards">
    <div class="card">
        <small>Total Guru</small>
        <div class="value">{{ $totalTeachers }}</div>
        <small>Aktif mengajar</small>
    </div>
    <div class="card">
        <small>Total Siswa</small>
        <div class="value">{{ $totalStudents }}</div>
        <small>Terdaftar</small>
    </div>
    <div class="card">
        <small>Kelas</small>
        <div class="value">{{ $totalClasses }}</div>
        <small>Rombel</small>
    </div>
</div>

<div class="panel">
    <h3>Statistik Siswa per Angkatan</h3>
    <div class="placeholder">Bar Chart Placeholder</div>
</div>
@endsection
