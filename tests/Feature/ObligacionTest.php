<?php

namespace Tests\Feature;

use App\Models\Obligacion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreaMercado;
use Tests\TestCase;

class ObligacionTest extends TestCase
{
    use RefreshDatabase, CreaMercado;

    public function test_emitir_calcula_el_importe_y_no_duplica(): void
    {
        [$puesto, $comerciante] = $this->escenario('ocupado');
        $this->asignacion($puesto, $comerciante, now()->startOfMonth()->subMonth());

        $this->post(route('obligaciones.emitir'), ['periodo' => now()->format('Y-m')])
            ->assertSessionHasNoErrors();

        $obligacion = Obligacion::firstOrFail();
        $this->assertSame('360.00', $obligacion->importe);   // 12 m² × Bs 30
        $this->assertSame('pendiente', $obligacion->estado);
        $this->assertSame(10, $obligacion->fecha_vencimiento->day);

        $this->post(route('obligaciones.emitir'), ['periodo' => now()->format('Y-m')])
            ->assertSessionHas('error');
        $this->assertDatabaseCount('obligaciones', 1);
    }

    public function test_no_factura_una_asignacion_cerrada_antes_del_periodo(): void
    {
        [$puesto, $comerciante] = $this->escenario();
        $this->asignacion(
            $puesto,
            $comerciante,
            now()->startOfMonth()->subMonths(2),
            now()->startOfMonth()->subDays(5)
        );

        $this->post(route('obligaciones.emitir'), ['periodo' => now()->format('Y-m')])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('obligaciones', 0);
    }

    public function test_no_emite_periodos_futuros(): void
    {
        $this->post(route('obligaciones.emitir'), ['periodo' => now()->addMonth()->format('Y-m')])
            ->assertSessionHasErrors('periodo');
    }
}
