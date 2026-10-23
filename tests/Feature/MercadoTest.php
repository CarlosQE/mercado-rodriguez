<?php

namespace Tests\Feature;

use App\Models\DatoMercado;
use App\Models\FotoMercado;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Concerns\CreaMercado;
use Tests\TestCase;

class MercadoTest extends TestCase
{
    use RefreshDatabase, CreaMercado;

    public function test_las_paginas_publicas_responden_sin_sesion(): void
    {
        DatoMercado::factory()->create();
        FotoMercado::factory()->create(['ruta' => 'img/mercado/foto-1.svg']);
        $this->escenario();

        $this->get(route('inicio'))->assertOk()->assertSee('Nuestra historia');
        $this->get(route('puestos.libres'))->assertOk()->assertSee('Puestos libres');
    }

    public function test_el_accesorio_url_reconoce_relleno_y_subidas(): void
    {
        $relleno = FotoMercado::factory()->create(['ruta' => 'img/mercado/foto-1.svg']);
        $subida = FotoMercado::factory()->create(['ruta' => 'mercado/foto.jpg']);

        $this->assertSame(asset('img/mercado/foto-1.svg'), $relleno->url);
        $this->assertSame(asset('storage/mercado/foto.jpg'), $subida->url);
    }

    public function test_un_cobrador_y_un_visitante_no_administran_la_ficha(): void
    {
        DatoMercado::factory()->create();
        $foto = FotoMercado::factory()->create();

        $this->get(route('admin.mercado.edit'))->assertRedirect(route('login'));

        $this->actingAs($this->usuario(User::ROL_COBRADOR));

        $this->get(route('admin.mercado.edit'))->assertForbidden();
        $this->get(route('admin.fotos.index'))->assertForbidden();
        $this->post(route('admin.fotos.store'), ['titulo' => 'Intento'])->assertForbidden();
        $this->delete(route('admin.fotos.destroy', $foto))->assertForbidden();
    }

    public function test_el_admin_edita_la_ficha_y_sube_fotos(): void
    {
        Storage::fake('public');
        DatoMercado::factory()->create(['nombre' => 'Nombre anterior']);

        $this->actingAs($this->usuario(User::ROL_ADMIN));

        $this->put(route('admin.mercado.update'), [
            'nombre' => 'Mercado Rodríguez',
            'eslogan' => 'Un mercado de barrio.',
            'historia' => "Primer párrafo.\n\nSegundo párrafo.",
            'direccion' => 'Av. Rodríguez N.º 1420, La Paz',
            'referencia' => 'Frente a la plaza',
            'latitud' => '-16.4955000',
            'longitud' => '-68.1330000',
            'horario_atencion' => 'Lunes a sábado, de 06:00 a 19:00',
            'telefono' => '+591 2 245 6789',
            'email' => 'contacto@mercadorodriguez.test',
        ])->assertRedirect(route('admin.mercado.edit'));

        $this->assertDatabaseHas('datos_mercado', ['nombre' => 'Mercado Rodríguez', 'referencia' => 'Frente a la plaza']);

        $this->post(route('admin.fotos.store'), [
            'titulo' => 'Nueva foto',
            'descripcion' => 'Pasillo principal',
            'orden' => 1,
            'imagen' => UploadedFile::fake()->image('pasillo.jpg', 1600, 900),
        ])->assertRedirect(route('admin.fotos.index'));

        $foto = FotoMercado::where('titulo', 'Nueva foto')->firstOrFail();
        $this->assertStringStartsWith('mercado/', $foto->ruta);
        Storage::disk('public')->assertExists($foto->ruta);
    }
}
