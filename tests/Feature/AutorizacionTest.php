<?php

namespace Tests\Feature;

use App\Models\Obligacion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreaMercado;
use Tests\TestCase;

class AutorizacionTest extends TestCase
{
    use RefreshDatabase, CreaMercado;

    public function test_un_visitante_es_redirigido_al_login(): void
    {
        $this->get(route('comerciantes.index'))->assertRedirect(route('login'));
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_el_cobrador_consulta_los_modulos_y_registra_pagos(): void
    {
        $this->actingAs($this->usuario(User::ROL_COBRADOR));

        foreach (['secciones.index', 'puestos.index', 'comerciantes.index', 'asignaciones.index', 'obligaciones.index', 'pagos.index'] as $ruta) {
            $this->get(route($ruta))->assertOk();
        }

        [$puesto, $comerciante] = $this->escenario('ocupado');
        $asignacion = $this->asignacion($puesto, $comerciante, today()->subMonth());
        $obligacion = Obligacion::factory()->create([
            'asignacion_id' => $asignacion->id,
            'periodo' => now()->startOfMonth(),
            'fecha_vencimiento' => now()->startOfMonth()->addDays(9),
            'importe' => '360.00',
        ]);

        $this->post(route('pagos.store'), [
            'comerciante_id' => $comerciante->id,
            'obligaciones' => [$obligacion->id],
            'fecha_pago' => today()->toDateString(),
            'metodo' => 'efectivo',
        ])->assertRedirect();

        $this->assertSame('pagada', $obligacion->fresh()->estado);
    }

    public function test_el_cobrador_no_administra_el_mercado(): void
    {
        $this->actingAs($this->usuario(User::ROL_COBRADOR));

        $this->get(route('comerciantes.create'))->assertForbidden();
        $this->post(route('obligaciones.emitir'), ['periodo' => now()->format('Y-m')])->assertForbidden();
        $this->get(route('dashboard'))->assertForbidden();
    }

    public function test_el_admin_puede_administrar_y_abrir_el_dashboard(): void
    {
        $this->actingAs($this->usuario(User::ROL_ADMIN));

        $this->get(route('dashboard'))->assertOk();
        $this->get(route('comerciantes.create'))->assertOk();
        $this->get(route('puestos.create'))->assertOk();
        $this->post(route('obligaciones.emitir'), ['periodo' => now()->format('Y-m')])
            ->assertSessionHas('error');
    }

    public function test_el_login_redirige_segun_el_rol(): void
    {
        $admin = $this->usuario(User::ROL_ADMIN);

        $this->post(route('login.store'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($admin);
        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();

        $cobrador = $this->usuario(User::ROL_COBRADOR);

        $this->post(route('login.store'), [
            'email' => $cobrador->email,
            'password' => 'password',
        ])->assertRedirect(route('puestos.index'));
    }
}
