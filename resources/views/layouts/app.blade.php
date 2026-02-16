<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>
    <link rel="icon" type="image/png" href="{{ asset('storage/icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="min-h-screen">
        @unless (request()->routeIs('login'))
            <nav class="border-b bg-white">
                <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        @auth
                            <a href="{{ auth()->user()->role === 'guru' ? route('guru.dashboard') : route('admin.dashboard') }}" class="font-semibold">
                                {{ auth()->user()->role === 'guru' ? 'Dashboard Guru' : 'Admin Panel' }}
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="font-semibold">Admin Panel</a>
                        @endauth
                        @auth
                            @if (auth()->user()->role === 'admin')
                                <a href="{{ route('admin.guru.index') }}">Guru</a>
                                <a href="{{ route('admin.siswa.index') }}">Siswa</a>
                                <a href="{{ route('admin.kelas.index') }}">Kelas</a>
                                <a href="{{ route('admin.mapel.index') }}">Mapel</a>
                                <a href="{{ route('admin.users.index') }}">Users</a>
                            @elseif (auth()->user()->role === 'guru')
                                <a href="{{ route('guru.dashboard') }}">Dashboard Guru</a>
                            @endif
                        @endauth
                    </div>
                    @auth
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">Logout</button>
                        </form>
                    @endauth
                </div>
            </nav>
        @endunless
        <main class="{{ request()->routeIs('login') ? 'w-full p-0 m-0' : 'max-w-7xl mx-auto px-4 py-6' }}">
            @if (session('status'))
                <div class="mb-4 text-green-700">
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->any() && !request()->routeIs('login'))
                <div class="mb-4 text-red-600">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</body>
</html>


