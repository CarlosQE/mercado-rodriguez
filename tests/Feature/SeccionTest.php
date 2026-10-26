<?php

namespace Tests\Feature;

use App\Models\Seccion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreaMercado;
use Tests\TestCase;

class SeccionTest extends TestCase
{
    use RefreshDatabase, CreaMercado;

    public function test_crea_una_seccion_con_el_prefijo_en_mayusculas(): void
    {
        $this->actingAs($this->usuario());

        $this->post(route('secciones.store'), [
            'nombre' => 'Verduras',
            'prefijo' => 'ver',
            'tarifa_m2' => '18.00',
        ])->assertRedirect(route('secciones.index'));

        $this->assertDatabaseHas('secciones', [
            'nombre' => 'Verduras',
            'prefijo' => 'VER',
        ]);
    }

    public function test_rechaza_un_prefijo_repetido(): void
    {
        Seccion::factory()->create(['prefijo' => 'VER']);
        $this->actingAs($this->usuario());

        $this->post(route('secciones.store'), [
            'nombre' => 'Otra sección',
            'prefijo' => 'ver',
            'tarifa_m2' => '18.00',
        ])->assertSessionHasErrors([
            'prefijo' => 'Ese prefijo ya está en uso.',
        ]);

        $this->assertDatabaseCount('secciones', 1);
    }

    public function test_rechaza_prefijos_con_longitud_o_caracteres_invalidos(): void
    {
        $this->actingAs($this->usuario());

        foreach (['VE', 'VERD', 'V3R'] as $indice => $prefijo) {
            $this->post(route('secciones.store'), [
                'nombre' => 'Sección inválida '.$indice,
                'prefijo' => $prefijo,
                'tarifa_m2' => '18.00',
            ])->assertSessionHasErrors('prefijo');
        }

        $this->assertDatabaseCount('secciones', 0);
    }

    public function test_el_prefijo_no_cambia_al_editar_la_seccion(): void
    {
        $seccion = Seccion::factory()->create([
            'nombre' => 'Carnes',
            'prefijo' => 'CAR',
            'tarifa_m2' => '30.00',
        ]);
        $this->actingAs($this->usuario());

        $this->put(route('secciones.update', $seccion), [
            'nombre' => 'Carnes y embutidos',
            'prefijo' => 'OTR',
            'tarifa_m2' => '31.00',
        ])->assertRedirect(route('secciones.index'));

        $this->assertDatabaseHas('secciones', [
            'id' => $seccion->id,
            'nombre' => 'Carnes y embutidos',
            'prefijo' => 'CAR',
            'tarifa_m2' => '31.00',
        ]);
        $this->assertDatabaseMissing('secciones', ['prefijo' => 'OTR']);
    }
}
