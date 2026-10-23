<?php

namespace Tests\Feature\Concerns;

use App\Models\Asignacion;
use App\Models\Comerciante;
use App\Models\Puesto;
use App\Models\Seccion;
use App\Models\User;

trait CreaMercado
{
    /** Crea un usuario con el rol indicado para actuar en las pruebas. */
    protected function usuario(string $rol = User::ROL_ADMIN): User
    {
        return User::factory()->create(['rol' => $rol]);
    }

    /** Crea una sección (Bs 30/m²), un puesto de 12 m² y un comerciante activo. */
    protected function escenario(string $estado = 'libre'): array
    {
        $seccion = Seccion::factory()->create(['tarifa_m2' => '30.00']);
        $puesto = Puesto::factory()->create([
            'seccion_id'    => $seccion->id,
            'estado'        => $estado,
            'superficie_m2' => '12.00',
        ]);
        $comerciante = Comerciante::factory()->create(['activo' => true]);

        return [$puesto, $comerciante];
    }

    protected function asignacion(Puesto $puesto, Comerciante $comerciante, $inicio, $fin = null): Asignacion
    {
        return Asignacion::factory()->create([
            'puesto_id'      => $puesto->id,
            'comerciante_id' => $comerciante->id,
            'fecha_inicio'   => $inicio,
            'fecha_fin'      => $fin,
            'motivo'         => 'Adjudicación inicial',
        ]);
    }
}
