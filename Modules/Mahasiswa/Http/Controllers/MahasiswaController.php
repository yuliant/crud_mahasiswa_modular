<?php

namespace Modules\Mahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Mahasiswa\Entities\Mahasiswa;
use Modules\Prodi\Entities\Prodi;

class MahasiswaController extends Controller
{
    public function index(): View
    {
        return view('mahasiswa::index', [
            'mahasiswa' => Mahasiswa::with('prodi')->orderBy('id')->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('mahasiswa::form', [
            'mahasiswa' => new Mahasiswa,
            'prodi' => Prodi::orderBy('nama_prodi')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Mahasiswa::create($this->validated($request));

        return to_route('mahasiswa.index')->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    public function edit(Mahasiswa $mahasiswa): View
    {
        return view('mahasiswa::form', [
            'mahasiswa' => $mahasiswa,
            'prodi' => Prodi::orderBy('nama_prodi')->get(),
        ]);
    }

    public function update(Request $request, Mahasiswa $mahasiswa): RedirectResponse
    {
        $mahasiswa->update($this->validated($request, $mahasiswa));

        return to_route('mahasiswa.index')->with('success', 'Mahasiswa berhasil diperbarui.');
    }

    public function confirmDelete(Mahasiswa $mahasiswa): View
    {
        return view('confirm-delete', [
            'title' => 'Hapus Mahasiswa',
            'action' => route('mahasiswa.destroy', $mahasiswa),
            'back' => route('mahasiswa.index'),
            'details' => [
                'ID' => $mahasiswa->id,
                'Nama' => $mahasiswa->nama,
                'NIM' => $mahasiswa->nim,
                'Prodi' => $mahasiswa->prodi->nama_prodi,
            ],
            'blocked' => false,
        ]);
    }

    public function destroy(Mahasiswa $mahasiswa): RedirectResponse
    {
        $mahasiswa->delete();

        return to_route('mahasiswa.index')->with('success', 'Mahasiswa berhasil dihapus.');
    }

    private function validated(Request $request, ?Mahasiswa $mahasiswa = null): array
    {
        return $request->validate([
            'id_prodi' => ['required', 'integer', 'exists:prodi,id'],
            'nama' => ['required', 'string', 'max:100'],
            'nim' => ['required', 'string', 'max:30', Rule::unique('mahasiswa', 'nim')->ignore($mahasiswa)],
        ], [
            'id_prodi.required' => 'Silakan pilih prodi.',
            'id_prodi.integer' => 'Prodi tidak valid.',
            'id_prodi.exists' => 'Prodi yang dipilih tidak ditemukan.',
            'nama.required' => 'Nama mahasiswa wajib diisi.',
            'nama.string' => 'Nama mahasiswa harus berupa teks.',
            'nama.max' => 'Nama mahasiswa maksimal 100 karakter.',
            'nim.required' => 'NIM wajib diisi.',
            'nim.string' => 'NIM harus berupa teks.',
            'nim.max' => 'NIM maksimal 30 karakter.',
            'nim.unique' => 'NIM sudah digunakan mahasiswa lain.',
        ]);
    }
}
