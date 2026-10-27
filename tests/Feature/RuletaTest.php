<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreaMercado;
use Tests\TestCase;

class RuletaTest extends TestCase
{
    use RefreshDatabase, CreaMercado;

    public function test_un_visitante_ve_la_ruleta_y_su_enlace(): void
    {
        $respuesta = $this->get(route('ruleta'))->assertOk();

        $respuesta->assertSee('Ruleta de preguntas');
        $respuesta->assertSee('Gira la ruleta para recibir tu primera pregunta.');
        $respuesta->assertSee('HTML');
        $respuesta->assertSee('Proyecto 2');
        $respuesta->assertViewHas('categorias', fn ($categorias) => count($categorias) === 8);
        $respuesta->assertViewHas('preguntas', fn ($preguntas) => count($preguntas) === 8
            && collect($preguntas)->every(fn ($banco) => count($banco) === 2));
    }

    public function test_la_portada_muestra_el_banner_de_la_ruleta_al_visitante(): void
    {
        $this->get(route('inicio'))
            ->assertOk()
            ->assertSee(route('ruleta'))
            ->assertSee('Ruleta')
            ->assertSee('¿Cuánto sabes de web?')
            ->assertSee('Gira la ruleta');
    }

    public function test_el_admin_no_ve_la_ruleta_y_es_redirigido_al_dashboard(): void
    {
        $this->actingAs($this->usuario(User::ROL_ADMIN));

        $this->get(route('inicio'))
            ->assertOk()
            ->assertDontSee(route('ruleta'))
            ->assertDontSee('¿Cuánto sabes de web?');

        $this->get(route('ruleta'))->assertRedirect(route('dashboard'));
    }

    public function test_el_cobrador_no_ve_la_ruleta_y_es_redirigido_al_mapa(): void
    {
        $this->actingAs($this->usuario(User::ROL_COBRADOR));

        $this->get(route('inicio'))
            ->assertOk()
            ->assertDontSee(route('ruleta'))
            ->assertDontSee('¿Cuánto sabes de web?');

        $this->get(route('ruleta'))->assertRedirect(route('mapa.index'));
    }
}
