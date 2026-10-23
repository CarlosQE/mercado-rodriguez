<?php

namespace Tests\Feature;

use App\Models\Comerciante;
use App\Models\Obligacion;
use App\Models\Pago;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreaMercado;
use Tests\TestCase;

class PagoTest extends TestCase
{
    use RefreshDatabase, CreaMercado;

    private function obligacion($asignacion, int $mesesAtras, string $importe, string $estado = 'pendiente'): Obligacion
    {
        $periodo = now()->startOfMonth()->subMonths($mesesAtras);

        return Obligacion::factory()->create([
            'asignacion_id'     => $asignacion->id,
            'periodo'           => $periodo->toDateString(),
            'fecha_vencimiento' => $periodo->copy()->addDays(9)->toDateString(),
            'superficie_m2'     => '10.00',
            'tarifa_m2'         => '10.00',
            'importe'           => $importe,
            'estado'            => $estado,
        ]);
    }

    private function pagar(Comerciante $comerciante, array $ids)
    {
        return $this->post(route('pagos.store'), [
            'comerciante_id' => $comerciante->id,
            'obligaciones'   => $ids,
            'fecha_pago'     => today()->toDateString(),
            'metodo'         => 'efectivo',
        ]);
    }

    public function test_un_pago_marca_las_obligaciones_como_pagadas(): void
    {
        [$puesto, $comerciante] = $this->escenario('ocupado');
        $asignacion = $this->asignacion($puesto, $comerciante, today()->subMonths(4));
        $o1 = $this->obligacion($asignacion, 2, '100.00');
        $o2 = $this->obligacion($asignacion, 1, '200.00');

        $this->pagar($comerciante, [$o1->id, $o2->id])->assertSessionHasNoErrors();

        $pago = Pago::firstOrFail();
        $this->assertSame('MR-00001', $pago->comprobante);
        $this->assertSame('300.00', $pago->total);
        $this->assertSame('pagada', $o1->fresh()->estado);
        $this->assertSame('pagada', $o2->fresh()->estado);
        $this->assertDatabaseCount('pago_obligacion', 2);
    }

    public function test_el_comprobante_es_correlativo(): void
    {
        [$puesto, $comerciante] = $this->escenario('ocupado');
        $asignacion = $this->asignacion($puesto, $comerciante, today()->subMonths(4));
        $o1 = $this->obligacion($asignacion, 2, '100.00');
        $o2 = $this->obligacion($asignacion, 1, '200.00');

        $this->pagar($comerciante, [$o1->id]);
        $this->pagar($comerciante, [$o2->id]);

        $this->assertSame(
            ['MR-00001', 'MR-00002'],
            Pago::orderBy('id')->pluck('comprobante')->all()
        );
    }

    public function test_rechaza_obligaciones_de_otro_comerciante(): void
    {
        [$puesto, $titular] = $this->escenario('ocupado');
        $asignacion = $this->asignacion($puesto, $titular, today()->subMonths(4));
        $ajena = $this->obligacion($asignacion, 1, '100.00');
        $otro = Comerciante::factory()->create(['activo' => true]);

        $this->pagar($otro, [$ajena->id])->assertSessionHasErrors('obligaciones');

        $this->assertDatabaseCount('pagos', 0);
        $this->assertSame('pendiente', $ajena->fresh()->estado);
    }

    public function test_rechaza_una_obligacion_ya_pagada(): void
    {
        [$puesto, $comerciante] = $this->escenario('ocupado');
        $asignacion = $this->asignacion($puesto, $comerciante, today()->subMonths(4));
        $pagada = $this->obligacion($asignacion, 1, '100.00', 'pagada');

        $this->pagar($comerciante, [$pagada->id])->assertSessionHasErrors('obligaciones.0');

        $this->assertDatabaseCount('pagos', 0);
    }
}
