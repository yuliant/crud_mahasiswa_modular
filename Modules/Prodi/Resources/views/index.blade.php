@extends('layouts.app')
@section('title', 'Data Prodi')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h1 class="h4 mb-0">Data Prodi</h1>
        <a href="{{ route('prodi.create') }}" class="btn btn-primary">Tambah Prodi</a>
    </div>

    <div class="table-responsive bg-white border rounded">
        <table class="table table-striped table-hover align-middle mb-0">
            <caption class="visually-hidden">Daftar program studi</caption>
            <thead class="table-light">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nama Prodi</th>
                    <th scope="col" class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($prodi as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->nama_prodi }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('prodi.edit', $item) }}" class="btn btn-sm btn-outline-primary" aria-label="Edit prodi {{ $item->nama_prodi }}">Edit</a>
                            <a href="{{ route('prodi.delete', $item) }}" class="btn btn-sm btn-outline-danger" aria-label="Hapus prodi {{ $item->nama_prodi }}">Hapus</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-secondary py-4">Belum ada data prodi. Silakan tambah prodi terlebih dahulu.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $prodi->links() }}</div>
@endsection
