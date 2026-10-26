<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreaMercado;
use Tests\TestCase;

class NavegacionTest extends TestCase
{
    use RefreshDatabase, CreaMercado;

    public function test_el_visitante_ve_la_navegacion_publica(): void
    {
        $respuesta = $this->get(route('inicio'))->assertOk();

        $respuesta->assertSee('El mercado');
        $respuesta->assertSee('Tablero');
        $respuesta->assertSee('Puestos libres');
        $respuesta->assertSee('Ingresar');
        $respuesta->assertDontSee('Gestión');
        $respuesta->assertDontSee('Datos del mercado');
    }

    public function test_el_admin_ve_los_grupos_y_todos_sus_enlaces(): void
    {
        $this->actingAs($this->usuario(User::ROL_ADMIN));

        $respuesta = $this->get(route('dashboard'))->assertOk();

        $respuesta->assertSee('Gestión');
        $respuesta->assertSee('Cobros');
        $respuesta->assertSee('Sitio público');
        $respuesta->assertSee('Comerciantes');
        $respuesta->assertSee('Asignaciones');
        $respuesta->assertSee('Obligaciones');
        $respuesta->assertSee('Pagos');
        $respuesta->assertSee('Comunicados');
        $respuesta->assertSee('Fotos');
        $respuesta->assertSee('Datos del mercado');
        $respuesta->assertSee('Admin');
        $respuesta->assertSee('Cerrar sesión');
    }

    public function test_el_cobrador_no_ve_los_enlaces_de_administracion(): void
    {
        $this->actingAs($this->usuario(User::ROL_COBRADOR));

        $respuesta = $this->get(route('mapa.index'))->assertOk();

        $respuesta->assertSee('Mapa');
        $respuesta->assertSee('Gestión');
        $respuesta->assertSee('Cobros');
        $respuesta->assertSee('Tablero');
        $respuesta->assertSee('Cobrador');
        $respuesta->assertSee('Cerrar sesión');
        $respuesta->assertDontSee('Dashboard');
        $respuesta->assertDontSee('Datos del mercado');
        $respuesta->assertDontSee('Comunicados');
        $respuesta->assertDontSee('Fotos');
        $respuesta->assertDontSee('Administración del Mercado');
    }
}
