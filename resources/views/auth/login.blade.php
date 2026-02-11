@extends('layouts.app')

@section('title', 'Login')

@section('content')
<style>
    :root {
        --ink: #0f172a;
        --muted: #6b7280;
        --bg: #eef5ff;
        --panel: #ffffff;
        --accent: #1e8e4f;
        --accent-2: #2563eb;
    }
    .login-shell {
        min-height: 100dvh;
        display: grid;
        grid-template-columns: 1.1fr 0.9fr;
        background: linear-gradient(180deg, #f5f9ff 0%, #eef5ff 100%);
    }
    .hero {
        padding: 48px 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    .blob {
        position: absolute;
        width: 520px;
        height: 420px;
        background: #d6e4ff;
        border-radius: 50% 45% 55% 48%;
        right: 10%;
        bottom: 6%;
        z-index: 0;
    }
    .card-illus {
        width: 520px;
        max-width: 90%;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 16px 32px rgba(15, 23, 42, 0.08);
        padding: 20px;
        position: relative;
        z-index: 2;
    }
    .illus-header {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .illus-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #1e88e5;
    }
    .illus-lines {
        flex: 1;
    }
    .illus-line {
        height: 8px;
        background: #e5e7eb;
        border-radius: 999px;
        margin: 6px 0;
    }
    .illus-line.short { width: 60%; }
    .illus-tag { display: flex; gap: 6px; margin-top: 6px; }
    .illus-tag span { display: inline-block; width: 26px; height: 8px; background: #1e8e4f; border-radius: 999px; }
    .floating {
        position: absolute;
        background: #fff;
        border-radius: 10px;
        padding: 10px;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
    }
    .float-1 { top: 14%; left: 12%; width: 180px; }
    .float-2 { top: 36%; right: 18%; width: 130px; }
    .float-3 { bottom: 20%; right: 30%; width: 100px; }
    .float-line { height: 8px; background: #e5e7eb; border-radius: 999px; margin: 6px 0; }
    .float-dot { width: 12px; height: 12px; border-radius: 50%; background: #1e88e5; }
    .form-panel {
        background: transparent;
        padding: 40px 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .form-card {
        width: min(520px, 100%);
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        box-shadow: 0 24px 50px rgba(15, 23, 42, 0.1);
        padding: 34px 30px;
    }
    .brand {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        font-size: 13px;
        color: var(--ink);
        margin-bottom: 24px;
    }
    .title {
        font-size: 26px;
        font-weight: 800;
        margin-bottom: 6px;
        color: var(--ink);
    }
    .subtitle {
        color: var(--muted);
        font-size: 13px;
        margin-bottom: 18px;
    }
    .field { margin-bottom: 12px; }
    .label { display: block; font-size: 12px; color: var(--muted); margin-bottom: 6px; }
    .input {
        width: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 13px;
        outline: none;
        background: #fff;
    }
    .input:focus {
        border-color: var(--accent-2);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }
    .btn {
        width: 100%;
        background: var(--accent);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        margin-top: 8px;
    }
    .help {
        margin-top: 12px;
        font-size: 12px;
        color: #1e88e5;
        text-decoration: none;
    }
    @media (max-width: 980px) {
        .login-shell { grid-template-columns: 1fr; }
        .hero { display: none; }
        .form-panel { padding: 24px 16px; }
        .form-card { padding: 26px 20px; border-radius: 14px; }
    }
</style>

<div class="login-shell">
    <section class="hero">
        <div class="blob"></div>
        <div class="floating float-1">
            <div class="illus-header">
                <div class="float-dot"></div>
                <div class="float-line" style="width: 80%;"></div>
            </div>
            <div class="float-line"></div>
            <div class="float-line" style="width: 60%;"></div>
        </div>
        <div class="floating float-2">
            <div class="float-line"></div>
            <div class="float-line" style="width: 70%;"></div>
        </div>
        <div class="floating float-3">
            <div class="float-line"></div>
        </div>
        <div class="card-illus">
            <div class="illus-header">
                <div class="illus-avatar"></div>
                <div class="illus-lines">
                    <div class="illus-line"></div>
                    <div class="illus-line short"></div>
                    <div class="illus-tag"><span></span><span></span></div>
                </div>
                <div style="width:20px;height:20px;border-radius:50%;background:#4f46e5;"></div>
            </div>
        </div>
    </section>

    <section class="form-panel">
        <div class="form-card">
            <div class="brand">
                <img src="{{ asset('storage/icon.png') }}" alt="Logo" style="display:inline-block;width:26px;height:26px;border-radius:6px;object-fit:cover;">
                SMART CICADAS
            </div>
            <div class="title">Log in</div>
            <div class="subtitle">Sistem penilaian mendukung identifikasi minat dan bakat siswa</div>

            <form method="POST" action="{{ route('login.attempt') }}">
                @csrf
                <div class="field">
                    <label class="label">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}" class="input" placeholder="Masukkan username" required>
                </div>
                <div class="field">
                    <label class="label">Password</label>
                    <input type="password" name="password" class="input" placeholder="********" required>
                </div>
                <button type="submit" class="btn">Masuk</button>
            </form>
            <a href="#" class="help">Perlu bantuan? Hubungi admin</a>
        </div>
    </section>
</div>
@endsection
