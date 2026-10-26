<?php

namespace Tests\Feature;

use App\Models\Puesto;
use App\Models\Seccion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreaMercado;
use Tests\TestCase;

class PuestoTest extends TestCase
{
    use RefreshDatabase, CreaMercado;

    public function test_el_codigo_se_genera_automaticamente_y_no_se_puede_sobrescribir(): void
    {
        $this->actingAs($this->usuario());
        $seccion = Seccion::create(['nombre' => 'Verduras', 'prefijo' => 'VER', 'tarifa_m2' => '18.00']);

        $this->get(route('puestos.create'))
            ->assertOk()
            ->assertSee('El código se asigna automáticamente al guardar.')
            ->assertDontSee('name="codigo"', false);

        $this->post(route('puestos.store'), [
            'seccion_id' => $seccion->id,
            'superficie_m2' => '10.00',
        ])->assertRedirect(route('puestos.index'));

        $this->assertDatabaseHas('puestos', [
            'seccion_id' => $seccion->id,
            'codigo' => 'VER-001',
        ]);

        $this->post(route('puestos.store'), [
            'seccion_id' => $seccion->id,
            'superficie_m2' => '12.00',
            'codigo' => 'MAN-999',
        ])->assertRedirect(route('puestos.index'));

        $this->assertDatabaseHas('puestos', ['codigo' => 'VER-002']);
        $this->assertDatabaseMissing('puestos', ['codigo' => 'MAN-999']);

        $puesto = Puesto::where('codigo', 'VER-001')->firstOrFail();

        $this->get(route('puestos.edit', $puesto))
            ->assertOk()
            ->assertSee('VER-001')
            ->assertDontSee('name="codigo"', false);

        $this->put(route('puestos.update', $puesto), [
            'seccion_id' => $seccion->id,
            'superficie_m2' => '11.00',
            'codigo' => 'OTRO-001',
            'estado' => 'libre',
        ])->assertRedirect(route('puestos.index'));

        $this->assertDatabaseHas('puestos', [
            'id' => $puesto->id,
            'codigo' => 'VER-001',
        ]);
        $this->assertDatabaseMissing('puestos', ['codigo' => 'OTRO-001']);
    }
}
