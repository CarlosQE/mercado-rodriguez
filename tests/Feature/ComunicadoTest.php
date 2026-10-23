<?php

namespace Tests\Feature;

use App\Models\Comunicado;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreaMercado;
use Tests\TestCase;

class ComunicadoTest extends TestCase
{
    use RefreshDatabase, CreaMercado;

    public function test_el_tablero_publico_muestra_solo_los_comunicados_vigentes(): void
    {
        $admin = $this->usuario();
        $hoy = today();

        $vigente = Comunicado::factory()->create([
            'user_id' => $admin->id,
            'titulo' => 'Aviso vigente del mercado',
            'fecha_publicacion' => $hoy->copy()->subDay(),
            'fecha_vencimiento' => $hoy->copy()->addDays(5),
            'activo' => true,
        ]);

        $sinVencimiento = Comunicado::factory()->create([
            'user_id' => $admin->id,
            'titulo' => 'Aviso sin fecha de vencimiento',
            'fecha_publicacion' => $hoy->copy()->subDays(2),
            'fecha_vencimiento' => null,
            'activo' => true,
        ]);

        $vencido = Comunicado::factory()->create([
            'user_id' => $admin->id,
            'titulo' => 'Aviso vencido del mercado',
            'fecha_publicacion' => $hoy->copy()->subDays(10),
            'fecha_vencimiento' => $hoy->copy()->subDay(),
            'activo' => true,
        ]);

        $inactivo = Comunicado::factory()->create([
            'user_id' => $admin->id,
            'titulo' => 'Aviso inactivo del mercado',
            'fecha_publicacion' => $hoy->copy()->subDays(3),
            'fecha_vencimiento' => $hoy->copy()->addDays(5),
            'activo' => false,
        ]);

        $this->get(route('tablero'))
            ->assertOk()
            ->assertSee($vigente->titulo)
            ->assertSee($sinVencimiento->titulo)
            ->assertDontSee($vencido->titulo)
            ->assertDontSee($inactivo->titulo);
    }

    public function test_el_cobrador_y_el_visitante_no_administran_comunicados(): void
    {
        $this->get(route('admin.comunicados.index'))->assertRedirect(route('login'));

        $this->actingAs($this->usuario(User::ROL_COBRADOR));

        $this->get(route('admin.comunicados.index'))->assertForbidden();
        $this->get(route('admin.comunicados.create'))->assertForbidden();
        $this->post(route('admin.comunicados.store'), [
            'titulo' => 'Intento de publicación',
            'contenido' => 'No debería guardarse.',
            'tipo' => 'general',
            'fecha_publicacion' => today()->toDateString(),
        ])->assertForbidden();
    }

    public function test_el_admin_crea_edita_y_elimina_un_comunicado(): void
    {
        $admin = $this->usuario();
        $this->actingAs($admin);

        $this->get(route('admin.comunicados.create'))->assertOk();

        $this->post(route('admin.comunicados.store'), [
            'titulo' => 'Comunicado de prueba',
            'contenido' => 'Contenido inicial del comunicado.',
            'tipo' => 'general',
            'fecha_publicacion' => today()->toDateString(),
            'fecha_vencimiento' => today()->addDays(10)->toDateString(),
            'fijado' => '1',
            'activo' => '1',
        ])->assertRedirect(route('admin.comunicados.index'));

        $comunicado = Comunicado::firstOrFail();
        $this->assertSame($admin->id, $comunicado->user_id);
        $this->assertTrue($comunicado->fijado);
        $this->assertTrue($comunicado->activo);

        $this->get(route('admin.comunicados.edit', $comunicado))->assertOk();

        $this->put(route('admin.comunicados.update', $comunicado), [
            'titulo' => 'Comunicado actualizado',
            'contenido' => 'Contenido actualizado del comunicado.',
            'tipo' => 'urgente',
            'fecha_publicacion' => today()->toDateString(),
            'fecha_vencimiento' => today()->addDays(20)->toDateString(),
            'fijado' => '1',
            'activo' => '1',
        ])->assertRedirect(route('admin.comunicados.index'));

        $this->assertDatabaseHas('comunicados', [
            'id' => $comunicado->id,
            'titulo' => 'Comunicado actualizado',
            'tipo' => 'urgente',
        ]);

        $this->delete(route('admin.comunicados.destroy', $comunicado))
            ->assertRedirect(route('admin.comunicados.index'));

        $this->assertDatabaseMissing('comunicados', ['id' => $comunicado->id]);
    }

    public function test_la_fecha_de_vencimiento_no_puede_ser_anterior_a_la_publicacion(): void
    {
        $this->actingAs($this->usuario());

        $this->from(route('admin.comunicados.create'))
            ->post(route('admin.comunicados.store'), [
                'titulo' => 'Comunicado con fechas inválidas',
                'contenido' => 'La fecha de vencimiento es anterior.',
                'tipo' => 'general',
                'fecha_publicacion' => today()->toDateString(),
                'fecha_vencimiento' => today()->subDay()->toDateString(),
            ])
            ->assertRedirect(route('admin.comunicados.create'))
            ->assertSessionHasErrors('fecha_vencimiento');

        $this->assertDatabaseCount('comunicados', 0);
    }
}
