@extends('layouts.app')
@section('title', $title)

@section('content')
    <h1 class="h4 mb-3">{{ $title }}</h1>
    <div class="card">
        <div class="card-body">
            <dl class="row mb-3">
                @foreach($details as $label => $value)
                    <dt class="col-sm-3">{{ $label }}</dt>
                    <dd class="col-sm-9">{{ $value }}</dd>
                @endforeach
            </dl>
            @if($blocked)
                <div class="alert alert-warning" role="alert">Prodi masih memiliki mahasiswa. Pindahkan atau hapus mahasiswa tersebut terlebih dahulu.</div>
                <a href="{{ $back }}" class="btn btn-outline-secondary">Kembali</a>
            @else
                <p>Yakin ingin menghapus data ini?</p>
                <form action="{{ $action }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                    <a href="{{ $back }}" class="btn btn-outline-secondary">Batal</a>
                </form>
            @endif
        </div>
    </div>
@endsection
