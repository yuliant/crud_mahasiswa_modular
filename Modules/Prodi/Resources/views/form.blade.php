@extends('layouts.app')
@section('title', $prodi->exists ? 'Edit Prodi' : 'Tambah Prodi')

@section('content')
    <h1 class="h4 mb-3">{{ $prodi->exists ? 'Edit Prodi' : 'Tambah Prodi' }}</h1>
    <div class="card">
        <div class="card-body">
            <form action="{{ $prodi->exists ? route('prodi.update', $prodi) : route('prodi.store') }}" method="POST">
                @csrf
                @if($prodi->exists)
                    @method('PUT')
                @endif
                <div class="mb-3">
                    <label for="nama_prodi" class="form-label">Nama Prodi</label>
                    <input type="text" name="nama_prodi" id="nama_prodi" class="form-control @error('nama_prodi') is-invalid @enderror"
                           value="{{ old('nama_prodi', $prodi->nama_prodi) }}" maxlength="100" required
                           @error('nama_prodi') aria-invalid="true" aria-describedby="nama_prodi-error" @enderror>
                    @error('nama_prodi')<div id="nama_prodi-error" class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('prodi.index') }}" class="btn btn-outline-secondary">Batal</a>
            </form>
        </div>
    </div>
@endsection
