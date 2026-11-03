@extends('layouts.app')
@section('title', 'Data Mahasiswa')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h1 class="h4 mb-0">Data Mahasiswa</h1>
        <a href="{{ route('mahasiswa.create') }}" class="btn btn-primary">Tambah Mahasiswa</a>
    </div>

    <div class="table-responsive bg-white border rounded">
        <table class="table table-striped table-hover align-middle mb-0">
            <caption class="visually-hidden">Daftar mahasiswa dan program studi</caption>
            <thead class="table-light">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nama Prodi</th>
                    <th scope="col">Nama</th>
                    <th scope="col">NIM</th>
                    <th scope="col" class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mahasiswa as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->prodi->nama_prodi }}</td>
                        <td>{{ $item->nama }}</td>
                        <td>{{ $item->nim }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('mahasiswa.edit', $item) }}" class="btn btn-sm btn-outline-primary" aria-label="Edit mahasiswa {{ $item->nama }}">Edit</a>
                            <a href="{{ route('mahasiswa.delete', $item) }}" class="btn btn-sm btn-outline-danger" aria-label="Hapus mahasiswa {{ $item->nama }}">Hapus</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-secondary py-4">Belum ada data mahasiswa.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $mahasiswa->links() }}</div>
@endsection
