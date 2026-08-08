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
            display: flex;
            align-items: center;
            justify-content: space-evenly;
            position: relative;
            overflow: hidden;
            padding: 40px;
            gap: 40px;
        }

        .hero-bg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            z-index: 0;
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(15, 23, 42, 0.4) 100%);
            z-index: 1;
        }

        .welcome-section {
            position: relative;
            z-index: 2;
            color: #fff;
            max-width: 480px;
            display: flex;
            flex-direction: column;
            gap: 18px;
            animation: fadeUp 1s ease-out;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .welcome-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            backdrop-filter: blur(10px);
            width: fit-content;
        }

        .welcome-title {
            font-size: 42px;
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -0.02em;
            text-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .welcome-desc {
            font-size: 15px;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.6;
        }

        .vision-box {
            background: rgba(0, 0, 0, 0.4);
            border-left: 4px solid var(--accent);
            padding: 18px 20px;
            border-radius: 0 12px 12px 0;
            backdrop-filter: blur(8px);
            margin-top: 10px;
        }

        .vision-box h4 {
            margin-bottom: 6px;
            font-size: 14px;
            color: #fff;
            font-weight: 700;
        }

        .vision-box p {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.75);
            line-height: 1.6;
            margin: 0;
        }

        .form-panel {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 420px;
            animation: fadeUp 1s ease-out 0.2s backwards;
        }

        .form-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 24px;
            box-shadow: 0 24px 50px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.2) inset;
            padding: 40px;
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
            margin-bottom: 24px;
        }

        .field {
            position: relative;
            margin-bottom: 16px;
        }

        .label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 8px;
        }

        .input {
            width: 100%;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 13px;
            outline: none;
            background: #f9fafb;
            transition: 0.2s;
        }

        .input:focus {
            background: #fff;
            border-color: var(--accent-2);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .input.input-error {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .toggle-password {
            position: absolute;
            right: 14px;
            top: 35px;
            cursor: pointer;
            color: #9ca3af;
            transition: 0.2s;
        }

        .toggle-password:hover {
            color: var(--ink);
        }

        .error-text {
            color: #ef4444;
            font-size: 11px;
            margin-top: 6px;
            display: none;
            font-weight: 500;
        }

        .error-text.show {
            display: block;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }

        .alert-error .alert-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #ef4444;
            flex-shrink: 0;
        }

        .btn {
            width: 100%;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 8px;
            transition: all .2s;
            box-shadow: 0 4px 12px rgba(30, 142, 79, 0.2);
        }

        .btn:hover {
            background: #167a42;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(30, 142, 79, 0.3);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .help {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: var(--muted);
            text-decoration: none;
            font-weight: 500;
            transition: 0.2s;
        }

        .help:hover {
            color: var(--accent-2);
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            20%,
            60% {
                transform: translateX(-4px);
            }

            40%,
            80% {
                transform: translateX(4px);
            }
        }

        .shake {
            animation: shake .35s ease;
        }

        @media (max-width: 980px) {
            .login-shell {
                flex-direction: column;
                padding: 20px;
                justify-content: center;
            }

            .welcome-section {
                display: none;
            }

            .form-panel {
                max-width: 100%;
            }

            .form-card {
                padding: 30px 24px;
                border-radius: 16px;
            }
        }
    </style>

    <div class="login-shell">
        <img src="{{ asset('images/sdn_cicaidas.jpg') }}" class="hero-bg" alt="SDN Cicaidas Background">
        <div class="hero-overlay"></div>

        <section class="welcome-section">
            <div class="welcome-badge" id="greetingBadge">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path
                        d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83" />
                </svg>
                <span id="greetingText">Selamat Datang</span>
            </div>
            <h1 class="welcome-title">Sistem Cerdas <br>SDN Cicadas</h1>
            <p class="welcome-desc">Sistem Penilaian Siswa - Minat dan Bakat </p>

            <div class="vision-box">
                <h4>Visi</h4>
                <p>Menjadi sekolah dasar yang unggul dalam prestasi akademik dan non-akademik, berbasis teknologi, serta
                    membentuk karakter siswa yang berakhlak mulia.</p>

                <h4 style="margin-top: 12px;">Misi</h4>
                <ul
                    style="padding-left: 16px; margin: 4px 0 0 0; font-size: 13px; color: rgba(255,255,255,0.75); line-height: 1.6; list-style-type: disc;">
                    <li style="margin-bottom: 4px;">Menyelenggarakan pendidikan berkualitas berbasis kurikulum nasional.
                    </li>
                    <li style="margin-bottom: 4px;">Mengembangkan potensi minat dan bakat siswa melalui kegiatan
                        ekstrakurikuler.</li>
                    <li style="margin-bottom: 4px;">Memanfaatkan teknologi informasi untuk mendukung proses pembelajaran.
                    </li>
                    <li style="margin-bottom: 4px;">Membentuk lingkungan sekolah yang aman, nyaman, dan kondusif.</li>
                </ul>
            </div>
        </section>

        <section class="form-panel">
            <div class="form-card">
                <div class="brand">
                    <img src="{{ asset('storage/icon.png') }}" alt="Logo"
                        style="display:inline-block;width:26px;height:26px;border-radius:6px;object-fit:cover;">
                    SMART CICADAS
                </div>
                <div class="title">Log in</div>
                <div class="subtitle">Masuk ke akun Anda untuk melanjutkan</div>

                @if ($errors->any())
                    <div class="alert-error">
                        <span class="alert-dot"></span>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.attempt') }}" id="loginForm" novalidate>
                    @csrf
                    <div class="field">
                        <label class="label">Username</label>
                        <input type="text" name="username" id="inputUsername" value="{{ old('username') }}"
                            class="input @error('username') input-error @enderror"
                            placeholder="Masukkan username admin/guru">
                        <div class="error-text" id="errUsername">Username wajib diisi.</div>
                    </div>
                    <div class="field">
                        <label class="label">Password</label>
                        <input type="password" name="password" id="inputPassword"
                            class="input @error('password') input-error @enderror" placeholder="********">
                        <svg class="toggle-password" id="togglePassword" width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <div class="error-text" id="errPassword">Password wajib diisi.</div>
                    </div>
                    <button type="submit" class="btn" id="btnLogin">Masuk ke Sistem</button>
                </form>
                <a href="#" class="help">Lupa password? Hubungi Administrator</a>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Dynamic Greeting
            const hour = new Date().getHours();
            const greetingText = document.getElementById('greetingText');
            if (hour >= 5 && hour < 11) greetingText.innerText = "Selamat Pagi!";
            else if (hour >= 11 && hour < 15) greetingText.innerText = "Selamat Siang!";
            else if (hour >= 15 && hour < 18) greetingText.innerText = "Selamat Sore!";
            else greetingText.innerText = "Selamat Malam!";

            // Password Toggle
            const togglePassword = document.getElementById('togglePassword');
            const inputPassword = document.getElementById('inputPassword');
            togglePassword.addEventListener('click', function (e) {
                const type = inputPassword.getAttribute('type') === 'password' ? 'text' : 'password';
                inputPassword.setAttribute('type', type);
                if (type === 'text') {
                    this.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
                } else {
                    this.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
                }
            });

            const form = document.getElementById('loginForm');
            const username = document.getElementById('inputUsername');
            const errUser = document.getElementById('errUsername');
            const errPass = document.getElementById('errPassword');
            const btn = document.getElementById('btnLogin');

            function clearErr(input, errEl) {
                input.addEventListener('input', function () {
                    input.classList.remove('input-error', 'shake');
                    errEl.classList.remove('show');
                });
            }
            clearErr(username, errUser);
            clearErr(inputPassword, errPass);

            form.addEventListener('submit', function (e) {
                let valid = true;

                if (!username.value.trim()) {
                    username.classList.add('input-error', 'shake');
                    errUser.classList.add('show');
                    valid = false;
                }
                if (!inputPassword.value) {
                    inputPassword.classList.add('input-error', 'shake');
                    errPass.classList.add('show');
                    valid = false;
                }

                if (!valid) {
                    e.preventDefault();
                    return;
                }

                btn.disabled = true;
                btn.textContent = 'Memproses...';
            });
        });
    </script>
@endsection