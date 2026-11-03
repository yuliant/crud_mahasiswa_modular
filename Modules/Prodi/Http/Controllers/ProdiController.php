<?php

namespace Modules\Prodi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Prodi\Entities\Prodi;

class ProdiController extends Controller
{
    public function index(): View
    {
        return view('prodi::index', ['prodi' => Prodi::orderBy('id')->paginate(10)]);
    }

    public function create(): View
    {
        return view('prodi::form', ['prodi' => new Prodi]);
    }

    public function store(Request $request): RedirectResponse
    {
        Prodi::create($this->validated($request));

        return to_route('prodi.index')->with('success', 'Prodi berhasil ditambahkan.');
    }

    public function edit(Prodi $prodi): View
    {
        return view('prodi::form', compact('prodi'));
    }

    public function update(Request $request, Prodi $prodi): RedirectResponse
    {
        $prodi->update($this->validated($request));

        return to_route('prodi.index')->with('success', 'Prodi berhasil diperbarui.');
    }

    public function confirmDelete(Prodi $prodi): View
    {
        return view('confirm-delete', [
            'title' => 'Hapus Prodi',
            'action' => route('prodi.destroy', $prodi),
            'back' => route('prodi.index'),
            'details' => ['ID' => $prodi->id, 'Nama Prodi' => $prodi->nama_prodi],
            'blocked' => $prodi->mahasiswa()->exists(),
        ]);
    }

    public function destroy(Prodi $prodi): RedirectResponse
    {
        if ($prodi->mahasiswa()->exists()) {
            return to_route('prodi.index')->with('error', 'Prodi masih memiliki mahasiswa. Pindahkan atau hapus mahasiswa tersebut terlebih dahulu.');
        }

        $prodi->delete();

        return to_route('prodi.index')->with('success', 'Prodi berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nama_prodi' => ['required', 'string', 'max:100'],
        ], [
            'nama_prodi.required' => 'Nama prodi wajib diisi.',
            'nama_prodi.string' => 'Nama prodi harus berupa teks.',
            'nama_prodi.max' => 'Nama prodi maksimal 100 karakter.',
        ]);
    }
}
