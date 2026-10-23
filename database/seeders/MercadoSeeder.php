<?php

namespace Database\Seeders;

use App\Models\DatoMercado;
use App\Models\FotoMercado;
use Illuminate\Database\Seeder;

class MercadoSeeder extends Seeder
{
    public function run(): void
    {
        DatoMercado::create([
            'nombre' => 'Mercado Rodríguez',
            'eslogan' => 'Un mercado de barrio con memoria, oficio y futuro.',
            'historia' => implode("\n\n", [
                'Cuenta la historia del barrio que el Mercado Rodríguez nació de una feria de domingo. Un grupo de familias colocaba sus mantas sobre la acera, ordenaba las primeras cajas de fruta y compartía un toldo cuando llegaba la lluvia. Con los años, el puesto improvisado se volvió una costumbre y la costumbre pidió un techo propio.',
                'La construcción se hizo por etapas. Primero llegaron los muros de la nave central y una fila de puestos de abarrotes; después se sumaron las cámaras de carnes, el pasillo de verduras y la zona de comida, donde el olor a caldo y a pan recién horneado marca el ritmo de la mañana. Cada ampliación conservó algo de la feria original: la atención cercana, el regateo amable y la costumbre de saludar por el nombre.',
                'Hoy el mercado es un pequeño ecosistema. Las familias comerciantes se organizan en secciones, cuidan la limpieza de los pasillos y comparten turnos para abrir y cerrar. Los fines de semana llegan compradores de otros barrios, atraídos por las frutas de temporada, las carnes frescas y los platos que se sirven hasta media tarde.',
                'Esta ficha es un ejercicio académico: el nombre, la historia, la dirección y los datos de contacto son ficticios y sirven para practicar el desarrollo de un sistema web. Cualquier parecido con un mercado real es una coincidencia del cariño con que se escribió.',
            ]),
            'direccion' => 'Av. Rodríguez N.º 1420, zona San Pedro, La Paz, Bolivia',
            'referencia' => 'A media cuadra de la plaza del barrio, ingreso principal por la calle lateral.',
            'latitud' => '-16.4955000',
            'longitud' => '-68.1330000',
            'horario_atencion' => 'Lunes a sábado, de 06:00 a 19:00',
            'telefono' => '+591 2 245 6789',
            'email' => 'contacto@mercadorodriguez.test',
        ]);

        $fotos = [
            ['Fachada del mercado', 'Composición abstracta del ingreso principal.', 'img/mercado/foto-1.svg'],
            ['Pasillo de frutas y verduras', 'Cajones y toldos en un pasillo luminoso.', 'img/mercado/foto-2.svg'],
            ['Zona de carnes', 'Volúmenes y superficies de la sección de carnes.', 'img/mercado/foto-3.svg'],
            ['Zona de comida', 'Mesas y platos servidos hasta media tarde.', 'img/mercado/foto-4.svg'],
            ['Vista general', 'Una mirada de conjunto a los puestos del mercado.', 'img/mercado/foto-5.svg'],
            ['Detalle de productos', 'Formas y colores de la mercadería de temporada.', 'img/mercado/foto-6.svg'],
        ];

        foreach ($fotos as $orden => [$titulo, $descripcion, $ruta]) {
            FotoMercado::create([
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'ruta' => $ruta,
                'orden' => $orden,
            ]);
        }
    }
}
