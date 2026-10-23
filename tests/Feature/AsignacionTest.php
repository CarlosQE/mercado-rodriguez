<?php

namespace Tests\Feature;

use App\Models\Comerciante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreaMercado;
use Tests\TestCase;

class AsignacionTest extends TestCase
{
    use RefreshDatabase, CreaMercado;

    public function test_crear_una_asignacion_ocupa_el_puesto(): void
    {
        [$puesto, $comerciante] = $this->escenario();

        $this->post(route('asignaciones.store'), [
            'puesto_id'      => $puesto->id,
            'comerciante_id' => $comerciante->id,
            'fecha_inicio'   => today()->toDateString(),
            'motivo'         => 'Adjudicación inicial',
        ])->assertRedirect(route('asignaciones.index'));

        $this->assertDatabaseHas('asignaciones', ['puesto_id' => $puesto->id, 'fecha_fin' => null]);
        $this->assertSame('ocupado', $puesto->fresh()->estado);
    }

    public function test_rechaza_una_asignacion_superpuesta(): void
    {
        [$puesto, $titular] = $this->escenario('ocupado');
        $this->asignacion($puesto, $titular, today()->subMonth());
        $otro = Comerciante::factory()->create(['activo' => true]);

        $this->post(route('asignaciones.store'), [
            'puesto_id'      => $puesto->id,
            'comerciante_id' => $otro->id,
            'fecha_inicio'   => today()->toDateString(),
            'motivo'         => 'Intento',
        ])->assertSessionHasErrors('fecha_inicio');

        $this->assertDatabaseCount('asignaciones', 1);
    }

    public function test_rechaza_un_comerciante_inactivo(): void
    {
        [$puesto] = $this->escenario();
        $inactivo = Comerciante::factory()->create(['activo' => false]);

        $this->post(route('asignaciones.store'), [
            'puesto_id'      => $puesto->id,
            'comerciante_id' => $inactivo->id,
            'fecha_inicio'   => today()->toDateString(),
            'motivo'         => 'Intento',
        ])->assertSessionHasErrors('comerciante_id');

        $this->assertSame('libre', $puesto->fresh()->estado);
    }

    public function test_cerrar_una_asignacion_libera_el_puesto(): void
    {
        [$puesto, $comerciante] = $this->escenario('ocupado');
        $asignacion = $this->asignacion($puesto, $comerciante, today()->subMonth());

        $this->patch(route('asignaciones.cerrar', $asignacion), [
            'fecha_fin' => today()->toDateString(),
        ])->assertSessionHasNoErrors();

        $this->assertSame(today()->toDateString(), $asignacion->fresh()->fecha_fin->toDateString());
        $this->assertSame('libre', $puesto->fresh()->estado);
    }
}
