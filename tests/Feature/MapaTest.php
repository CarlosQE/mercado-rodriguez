<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreaMercado;
use Tests\TestCase;

class MapaTest extends TestCase
{
    use RefreshDatabase, CreaMercado;

    public function test_el_cobrador_ve_el_mapa_y_la_ficha_del_puesto(): void
    {
        [$puesto, $comerciante] = $this->escenario('ocupado');
        $this->asignacion($puesto, $comerciante, today()->subMonth());

        $this->actingAs($this->usuario(User::ROL_COBRADOR));

        $this->get(route('mapa.index'))
            ->assertOk()
            ->assertSee($puesto->codigo);

        $this->get(route('puestos.show', $puesto))
            ->assertOk()
            ->assertSee($puesto->codigo)
            ->assertSee($comerciante->apellidos);
    }

    public function test_un_visitante_es_redirigido_al_login(): void
    {
        [$puesto] = $this->escenario();

        $this->get(route('mapa.index'))->assertRedirect(route('login'));
        $this->get(route('puestos.show', $puesto))->assertRedirect(route('login'));
    }
}
