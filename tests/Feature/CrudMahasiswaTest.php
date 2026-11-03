<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Modules\Mahasiswa\Entities\Mahasiswa;
use Modules\Prodi\Entities\Prodi;
use Nwidart\Modules\Facades\Module;
use Tests\TestCase;

class CrudMahasiswaTest extends TestCase
{
    use RefreshDatabase;

    public function test_modules_and_their_migrations_are_loaded(): void
    {
        $this->assertTrue(Module::find('Prodi')->isEnabled());
        $this->assertTrue(Module::find('Mahasiswa')->isEnabled());
        $this->assertEqualsCanonicalizing(['id', 'nama_prodi'], Schema::getColumnListing('prodi'));
        $this->assertEqualsCanonicalizing(['id', 'id_prodi', 'nama', 'nim'], Schema::getColumnListing('mahasiswa'));
        $this->assertFalse(Schema::hasTable('users'));
    }

    public function test_home_redirects_and_empty_lists_render(): void
    {
        $this->get('/')->assertRedirect('/mahasiswa');
        $this->get(route('prodi.index'))->assertOk()->assertSee('Belum ada data prodi');
        $this->get(route('mahasiswa.index'))->assertOk()->assertSee('Belum ada data mahasiswa');
        $this->get(route('prodi.create'))->assertOk()->assertSee('Tambah Prodi');
        $this->get(route('mahasiswa.create'))->assertOk()->assertSee('Belum ada prodi.');
    }

    public function test_prodi_can_be_created_updated_and_deleted(): void
    {
        $this->post(route('prodi.store'), ['nama_prodi' => 'Sistem Informasi'])
            ->assertRedirect(route('prodi.index'))->assertSessionHas('success');
        $prodi = Prodi::firstOrFail();
        $this->get(route('prodi.index'))->assertSee('Sistem Informasi');
        $this->get(route('prodi.edit', $prodi))->assertOk()->assertSee('Sistem Informasi');

        $this->put(route('prodi.update', $prodi), ['nama_prodi' => 'Informatika'])
            ->assertRedirect(route('prodi.index'));
        $this->assertDatabaseHas('prodi', ['id' => $prodi->id, 'nama_prodi' => 'Informatika']);
        $this->get(route('prodi.delete', $prodi))->assertOk()->assertSee('Ya, Hapus');
        $this->assertDatabaseCount('prodi', 1);

        $this->delete(route('prodi.destroy', $prodi))->assertRedirect(route('prodi.index'));
        $this->assertDatabaseCount('prodi', 0);
    }

    public function test_prodi_name_is_required_and_limited(): void
    {
        foreach (['', str_repeat('a', 101), ['invalid']] as $value) {
            $this->post(route('prodi.store'), ['nama_prodi' => $value])
                ->assertSessionHasErrors('nama_prodi');
        }
        $this->assertDatabaseCount('prodi', 0);
    }

    public function test_mahasiswa_crud_preserves_nim_and_can_change_prodi(): void
    {
        $prodi = $this->prodi();
        $other = $this->prodi('Informatika');
        $this->get(route('mahasiswa.create'))->assertOk()->assertSee('Sistem Informasi');
        $this->post(route('mahasiswa.store'), [
            'id_prodi' => $prodi->id, 'nama' => 'Rina', 'nim' => '001234',
        ])->assertRedirect(route('mahasiswa.index'))->assertSessionHas('success');

        $mahasiswa = Mahasiswa::firstOrFail();
        $this->assertSame('001234', $mahasiswa->nim);
        $this->assertTrue($mahasiswa->prodi->is($prodi));
        $this->assertTrue($prodi->mahasiswa->first()->is($mahasiswa));
        $this->get(route('mahasiswa.index'))->assertOk()->assertSee('Rina')->assertSee('Sistem Informasi');
        $this->get(route('mahasiswa.edit', $mahasiswa))->assertOk()->assertSee('001234');

        $this->put(route('mahasiswa.update', $mahasiswa), [
            'id_prodi' => $other->id, 'nama' => 'Rina Putri', 'nim' => '001234',
        ])->assertRedirect(route('mahasiswa.index'))->assertSessionHasNoErrors();
        $this->assertDatabaseHas('mahasiswa', [
            'id' => $mahasiswa->id, 'id_prodi' => $other->id, 'nama' => 'Rina Putri', 'nim' => '001234',
        ]);
        $this->get(route('mahasiswa.delete', $mahasiswa))->assertOk()->assertSee('Informatika')->assertSee('Ya, Hapus');
        $this->assertDatabaseCount('mahasiswa', 1);
        $this->delete(route('mahasiswa.destroy', $mahasiswa))->assertRedirect(route('mahasiswa.index'));
        $this->assertDatabaseCount('mahasiswa', 0);
    }

    public function test_mahasiswa_requires_valid_prodi_name_and_nim(): void
    {
        $this->post(route('mahasiswa.store'), [])->assertSessionHasErrors(['id_prodi', 'nama', 'nim']);
        $this->post(route('mahasiswa.store'), [
            'id_prodi' => 9999, 'nama' => str_repeat('a', 101), 'nim' => str_repeat('1', 31),
        ])->assertSessionHasErrors(['id_prodi', 'nama', 'nim']);
        $this->post(route('mahasiswa.store'), [
            'id_prodi' => 'invalid', 'nama' => ['invalid'], 'nim' => ['invalid'],
        ])->assertSessionHasErrors(['id_prodi', 'nama', 'nim']);
        $this->assertDatabaseCount('mahasiswa', 0);
    }

    public function test_nim_must_be_unique_on_create_and_update(): void
    {
        $prodi = $this->prodi();
        $first = Mahasiswa::create(['id_prodi' => $prodi->id, 'nama' => 'Rina', 'nim' => '001']);
        $second = Mahasiswa::create(['id_prodi' => $prodi->id, 'nama' => 'Budi', 'nim' => '002']);
        $this->post(route('mahasiswa.store'), [
            'id_prodi' => $prodi->id, 'nama' => 'Sari', 'nim' => $first->nim,
        ])->assertSessionHasErrors('nim');
        $this->put(route('mahasiswa.update', $second), [
            'id_prodi' => $prodi->id, 'nama' => 'Budi', 'nim' => $first->nim,
        ])->assertSessionHasErrors('nim');
        $this->assertSame('002', $second->fresh()->nim);
        $this->assertDatabaseCount('mahasiswa', 2);
    }

    public function test_invalid_form_retains_input_and_shows_errors(): void
    {
        $prodi = $this->prodi();
        $this->from(route('mahasiswa.create'))->post(route('mahasiswa.store'), [
            'id_prodi' => $prodi->id, 'nama' => 'Nama Dipertahankan', 'nim' => '',
        ])->assertRedirect(route('mahasiswa.create'))->assertSessionHasErrors('nim');
        $this->get(route('mahasiswa.create'))->assertOk()
            ->assertSee('Nama Dipertahankan')->assertSee('NIM wajib diisi.');
    }

    public function test_prodi_in_use_cannot_be_deleted(): void
    {
        $prodi = $this->prodi();
        Mahasiswa::create(['id_prodi' => $prodi->id, 'nama' => 'Rina', 'nim' => '001']);
        $this->get(route('prodi.delete', $prodi))->assertOk()
            ->assertSee('Prodi masih memiliki mahasiswa.')->assertDontSee('Ya, Hapus');
        $this->delete(route('prodi.destroy', $prodi))->assertRedirect(route('prodi.index'))
            ->assertSessionHas('error');
        $this->assertDatabaseCount('prodi', 1);
        $this->assertDatabaseCount('mahasiswa', 1);
    }

    public function test_database_enforces_prodi_foreign_key(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        Mahasiswa::create(['id_prodi' => 9999, 'nama' => 'Rina', 'nim' => '001']);
    }

    public function test_database_prevents_deleting_a_prodi_in_use(): void
    {
        $prodi = $this->prodi();
        Mahasiswa::create(['id_prodi' => $prodi->id, 'nama' => 'Rina', 'nim' => '001']);
        $this->expectException(\Illuminate\Database\QueryException::class);
        $prodi->delete();
    }

    public function test_missing_records_return_404(): void
    {
        foreach (['prodi', 'mahasiswa'] as $resource) {
            $this->get("/$resource/9999/edit")->assertNotFound();
            $this->get("/$resource/9999/hapus")->assertNotFound();
            $this->put("/$resource/9999", [])->assertNotFound();
            $this->delete("/$resource/9999")->assertNotFound();
        }
    }

    public function test_lists_use_bootstrap_pagination_and_escape_user_input(): void
    {
        for ($index = 1; $index <= 11; $index++) {
            $prodi = $this->prodi('Prodi '.$index);
            Mahasiswa::create(['id_prodi' => $prodi->id, 'nama' => 'Mahasiswa '.$index, 'nim' => (string) $index]);
        }
        $this->get(route('prodi.index'))->assertOk()->assertSee('page-item')
            ->assertViewHas('prodi', fn ($items) => $items->count() === 10 && $items->total() === 11);
        $this->get(route('mahasiswa.index', ['page' => 2]))->assertOk()->assertSee('Mahasiswa 11')
            ->assertViewHas('mahasiswa', fn ($items) => $items->count() === 1);
        $this->prodi('<script>alert(1)</script>');
        $this->get(route('prodi.index', ['page' => 2]))
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    private function prodi(string $name = 'Sistem Informasi'): Prodi
    {
        return Prodi::create(['nama_prodi' => $name]);
    }
}
