<?php

namespace Database\Seeders;

use App\Models\Comunicado;
use App\Models\User;
use Illuminate\Database\Seeder;

class ComunicadoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('rol', User::ROL_ADMIN)->first()
            ?? User::factory()->create(['rol' => User::ROL_ADMIN]);

        $hoy = today();

        $comunicados = [
            [
                'titulo' => 'Reunión de comerciantes del mercado',
                'contenido' => 'Convocamos a todas las familias comerciantes a la reunión mensual del mercado. Se tratarán el ordenamiento de pasillos, la limpieza de las áreas comunes y la organización de la próxima feria.',
                'tipo' => 'general',
                'fecha_publicacion' => $hoy->copy()->subDay(),
                'fecha_vencimiento' => $hoy->copy()->addDays(12),
                'fijado' => true,
                'activo' => true,
            ],
            [
                'titulo' => 'Corte de agua programado',
                'contenido' => 'El servicio de agua se interrumpirá el jueves de 08:00 a 14:00 por trabajos de mantenimiento en la red principal. Recomendamos almacenar agua suficiente para la atención del día.',
                'tipo' => 'urgente',
                'fecha_publicacion' => $hoy->copy()->subDays(2),
                'fecha_vencimiento' => null,
                'fijado' => false,
                'activo' => true,
            ],
            [
                'titulo' => 'Feria de fin de semana',
                'contenido' => 'Este sábado y domingo se realizará la feria de productos de temporada en la plaza del barrio. Los puestos interesados pueden inscribirse en la oficina de administración hasta el viernes.',
                'tipo' => 'evento',
                'fecha_publicacion' => $hoy->copy()->subDays(4),
                'fecha_vencimiento' => $hoy->copy()->addDays(8),
                'fijado' => false,
                'activo' => true,
            ],
            [
                'titulo' => 'Fecha límite para el pago de obligaciones',
                'contenido' => 'Recordamos que el pago de las obligaciones del período vence el día 10. Las personas con saldo pendiente deben acercarse a la oficina de cobranza para regularizar su situación.',
                'tipo' => 'cobranza',
                'fecha_publicacion' => $hoy->copy()->subDays(3),
                'fecha_vencimiento' => $hoy->copy()->addDays(5),
                'fijado' => false,
                'activo' => true,
            ],
            [
                'titulo' => 'Cierre por feriado municipal',
                'contenido' => 'El mercado permanecerá cerrado durante el feriado municipal. La atención se reanudará con el horario habitual al día siguiente.',
                'tipo' => 'general',
                'fecha_publicacion' => $hoy->copy()->subDays(30),
                'fecha_vencimiento' => $hoy->copy()->subDays(2),
                'fijado' => false,
                'activo' => true,
            ],
            [
                'titulo' => 'Mantenimiento de pasillos',
                'contenido' => 'Se realizarán trabajos de pintura y reparación en los pasillos centrales. El aviso quedó cerrado una vez concluidas las tareas.',
                'tipo' => 'general',
                'fecha_publicacion' => $hoy->copy()->subDays(15),
                'fecha_vencimiento' => $hoy->copy()->addDays(20),
                'fijado' => false,
                'activo' => false,
            ],
        ];

        foreach ($comunicados as $comunicado) {
            Comunicado::create([
                'user_id' => $admin->id,
                ...$comunicado,
            ]);
        }
    }
}
