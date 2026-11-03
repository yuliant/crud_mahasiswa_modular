<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'CRUD Mahasiswa') | CRUD Mahasiswa</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary" aria-label="Navigasi utama">
        <div class="container flex-wrap gap-2">
            <a class="navbar-brand" href="{{ route('mahasiswa.index') }}">CRUD Mahasiswa</a>
            <div class="navbar-nav flex-row gap-4">
                <a class="nav-link {{ request()->routeIs('prodi.*') ? 'active' : '' }}"
                   @if(request()->routeIs('prodi.*')) aria-current="page" @endif
                   href="{{ route('prodi.index') }}">Prodi</a>
                <a class="nav-link {{ request()->routeIs('mahasiswa.*') ? 'active' : '' }}"
                   @if(request()->routeIs('mahasiswa.*')) aria-current="page" @endif
                   href="{{ route('mahasiswa.index') }}">Mahasiswa</a>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        @if(session('success'))
            <div class="alert alert-success" role="status">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger" role="alert">Data belum tersimpan. Periksa isian formulir di bawah.</div>
        @endif

        @yield('content')
    </main>
</body>
</html>
