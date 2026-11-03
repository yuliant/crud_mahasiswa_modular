@extends('layouts.app')
@section('title', $mahasiswa->exists ? 'Edit Mahasiswa' : 'Tambah Mahasiswa')

@section('content')
    <h1 class="h4 mb-3">{{ $mahasiswa->exists ? 'Edit Mahasiswa' : 'Tambah Mahasiswa' }}</h1>
    @if($prodi->isEmpty())
        <div class="alert alert-warning" role="alert">
            Belum ada prodi. <a href="{{ route('prodi.create') }}" class="alert-link">Tambah prodi</a> sebelum menambahkan mahasiswa.
        </div>
    @endif
    <div class="card">
        <div class="card-body">
            <form action="{{ $mahasiswa->exists ? route('mahasiswa.update', $mahasiswa) : route('mahasiswa.store') }}" method="POST">
                @csrf
                @if($mahasiswa->exists)
                    @method('PUT')
                @endif
                <div class="mb-3">
                    <label for="id_prodi" class="form-label">Prodi</label>
                    <select name="id_prodi" id="id_prodi" class="form-select @error('id_prodi') is-invalid @enderror" required
                            @error('id_prodi') aria-invalid="true" aria-describedby="id_prodi-error" @enderror>
                        <option value="">Pilih prodi</option>
                        @foreach($prodi as $item)
                            <option value="{{ $item->id }}" @selected((string) old('id_prodi', $mahasiswa->id_prodi) === (string) $item->id)>{{ $item->nama_prodi }}</option>
                        @endforeach
                    </select>
                    @error('id_prodi')<div id="id_prodi-error" class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Mahasiswa</label>
                    <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror"
                           value="{{ old('nama', $mahasiswa->nama) }}" maxlength="100" required
                           @error('nama') aria-invalid="true" aria-describedby="nama-error" @enderror>
                    @error('nama')<div id="nama-error" class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="nim" class="form-label">NIM</label>
                    <input type="text" name="nim" id="nim" class="form-control @error('nim') is-invalid @enderror"
                           value="{{ old('nim', $mahasiswa->nim) }}" maxlength="30" required
                           @error('nim') aria-invalid="true" aria-describedby="nim-error" @enderror>
                    @error('nim')<div id="nim-error" class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button type="submit" class="btn btn-primary" @disabled($prodi->isEmpty())>Simpan</button>
                <a href="{{ route('mahasiswa.index') }}" class="btn btn-outline-secondary">Batal</a>
            </form>
        </div>
    </div>
@endsection
