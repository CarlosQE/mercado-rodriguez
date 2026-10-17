<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Carnes' => 30, 'Verduras' => 30, 'Abarrotes' => 60, 'Comida' => 15] as $nombre => $dias) {
            DB::table('secciones')->insertOrIgnore(['nombre' => $nombre, 'intervalo_inspeccion_dias' => $dias]);
        }
        $this->call(MercadoDemoSeeder::class);
    }
}
