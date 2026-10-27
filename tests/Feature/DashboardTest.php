<?php

namespace Tests\Feature;

use App\Models\Asignacion;
use App\Models\Comerciante;
use App\Models\Obligacion;
use App\Models\Pago;
use App\Models\Puesto;
use App\Models\Seccion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreaMercado;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase, CreaMercado;

    public function test_el_admin_ve_los_totales_del_dashboard(): void
    {
        $seccion = Seccion::factory()->create();
        Puesto::factory()->create(['seccion_id' => $seccion->id, 'estado' => 'ocupado']);
        Puesto::factory()->create(['seccion_id' => $seccion->id, 'estado' => 'libre']);
        Puesto::factory()->create(['seccion_id' => $seccion->id, 'estado' => 'clausurado']);

        $comerciante = Comerciante::factory()->create();
        $asignacion = Asignacion::factory()->create([
            'puesto_id' => Puesto::first()->id,
            'comerciante_id' => $comerciante->id,
            'fecha_inicio' => today()->subMonths(2),
        ]);

        Obligacion::factory()->create([
            'asignacion_id' => $asignacion->id,
            'periodo' => now()->startOfMonth()->subMonth(),
            'fecha_vencimiento' => today()->subDay(),
            'importe' => '200.00',
            'estado' => 'pendiente',
        ]);
        Obligacion::factory()->create([
            'asignacion_id' => $asignacion->id,
            'periodo' => now()->startOfMonth(),
            'fecha_vencimiento' => today()->addDays(3),
            'importe' => '100.00',
            'estado' => 'pendiente',
        ]);

        Pago::factory()->create(['total' => '150.00', 'fecha_pago' => today()]);
        Pago::factory()->create([
            'total' => '50.00',
            'fecha_pago' => now()->startOfMonth()->subMonth()->addDays(5),
        ]);

        $this->actingAs($this->usuario(User::ROL_ADMIN));

        $respuesta = $this->get(route('dashboard'));

        $respuesta->assertOk();
        $respuesta->assertViewHas('totalPuestos', 3);
        $respuesta->assertViewHas('ocupados', 1);
        $respuesta->assertViewHas('libres', 1);
        $respuesta->assertViewHas('clausurados', 1);
        $respuesta->assertViewHas('recaudadoMes', 150.0);
        $respuesta->assertViewHas('pendienteTotal', 300.0);
        $respuesta->assertViewHas('vencidasCantidad', 1);
        $respuesta->assertViewHas('vencidasMonto', 200.0);
        $respuesta->assertSee('Bs 150.00');
        $respuesta->assertSee('Bs 300.00');
    }

    public function test_los_meses_de_recaudacion_llegan_en_orden_cronologico(): void
    {
        $this->actingAs($this->usuario(User::ROL_ADMIN));

        $respuesta = $this->get(route('dashboard'))->assertOk();
        $meses = $respuesta->viewData('meses');

        $esperados = collect(range(5, 0))
            ->map(fn ($mesesAtras) => now()->startOfMonth()->subMonths($mesesAtras)->format('Y-m'))
            ->all();

        $this->assertCount(6, $meses);
        $this->assertSame($esperados, $meses->pluck('clave')->all());
        $this->assertSame(now()->format('Y-m'), $meses->last()['clave']);
    }

    public function test_el_cobrador_no_puede_abrir_el_dashboard(): void
    {
        $this->actingAs($this->usuario(User::ROL_COBRADOR));

        $this->get(route('dashboard'))->assertForbidden();
    }
}
