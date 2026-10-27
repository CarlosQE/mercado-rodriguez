<?php

namespace App\Http\Controllers;

class RuletaController extends Controller
{
    public function index()
    {
        $categorias = [
            ['clave' => 'html', 'nombre' => 'HTML', 'corta' => 'HTML'],
            ['clave' => 'css', 'nombre' => 'CSS', 'corta' => 'CSS'],
            ['clave' => 'javascript', 'nombre' => 'JavaScript', 'corta' => 'JavaScript'],
            ['clave' => 'http', 'nombre' => 'Web/HTTP', 'corta' => 'HTTP'],
            ['clave' => 'laravel', 'nombre' => 'Laravel', 'corta' => 'Laravel'],
            ['clave' => 'base-datos', 'nombre' => 'Base de datos', 'corta' => 'Base de datos'],
            ['clave' => 'proyecto-1', 'nombre' => 'Proyecto 1', 'corta' => 'Proyecto 1'],
            ['clave' => 'proyecto-2', 'nombre' => 'Proyecto 2', 'corta' => 'Proyecto 2'],
        ];

        $preguntas = [
            'html' => [
                [
                    'pregunta' => '¿Qué etiqueta se usa para crear un enlace?',
                    'opciones' => [
                        ['texto' => '<link>', 'correcta' => false],
                        ['texto' => '<a>', 'correcta' => true],
                        ['texto' => '<href>', 'correcta' => false],
                    ],
                    'explicacion' => 'La etiqueta <a> crea enlaces con el atributo href.',
                ],
                [
                    'pregunta' => '¿Qué etiqueta define el título principal de una página?',
                    'opciones' => [
                        ['texto' => '<h1>', 'correcta' => true],
                        ['texto' => '<head>', 'correcta' => false],
                        ['texto' => '<title-main>', 'correcta' => false],
                    ],
                    'explicacion' => '<h1> es el encabezado de mayor jerarquía.',
                ],
            ],
            'css' => [
                [
                    'pregunta' => '¿Para qué sirve CSS?',
                    'opciones' => [
                        ['texto' => 'Dar estilo y diseño a la página', 'correcta' => true],
                        ['texto' => 'Guardar datos', 'correcta' => false],
                        ['texto' => 'Enviar correos', 'correcta' => false],
                    ],
                    'explicacion' => 'CSS controla colores, tipografías y distribución.',
                ],
                [
                    'pregunta' => '¿Qué sistema de CSS acomoda elementos en filas o columnas flexibles?',
                    'opciones' => [
                        ['texto' => 'Flexbox', 'correcta' => true],
                        ['texto' => 'Cookies', 'correcta' => false],
                        ['texto' => 'Canvas', 'correcta' => false],
                    ],
                    'explicacion' => 'Flexbox distribuye y alinea elementos en una dimensión.',
                ],
            ],
            'javascript' => [
                [
                    'pregunta' => '¿Qué lenguaje se ejecuta en el navegador para dar interactividad?',
                    'opciones' => [
                        ['texto' => 'JavaScript', 'correcta' => true],
                        ['texto' => 'PostgreSQL', 'correcta' => false],
                        ['texto' => 'Composer', 'correcta' => false],
                    ],
                    'explicacion' => 'JavaScript corre en el navegador y reacciona a clics y eventos.',
                ],
                [
                    'pregunta' => '¿Qué palabra declara una constante en JavaScript?',
                    'opciones' => [
                        ['texto' => 'var', 'correcta' => false],
                        ['texto' => 'const', 'correcta' => true],
                        ['texto' => 'let-fijo', 'correcta' => false],
                    ],
                    'explicacion' => 'const declara un valor que no se reasigna.',
                ],
            ],
            'http' => [
                [
                    'pregunta' => '¿Qué código HTTP muestra nuestra página cuando alguien no tiene permiso?',
                    'opciones' => [
                        ['texto' => '200', 'correcta' => false],
                        ['texto' => '403', 'correcta' => true],
                        ['texto' => '500', 'correcta' => false],
                    ],
                    'explicacion' => '403 significa acceso prohibido; usamos una página 403 propia.',
                ],
                [
                    'pregunta' => '¿Qué protocolo muestra el candadito y cifra la conexión?',
                    'opciones' => [
                        ['texto' => 'HTTPS', 'correcta' => true],
                        ['texto' => 'FTP', 'correcta' => false],
                        ['texto' => 'SMTP', 'correcta' => false],
                    ],
                    'explicacion' => 'HTTPS cifra los datos entre el navegador y el servidor.',
                ],
            ],
            'laravel' => [
                [
                    'pregunta' => '¿En qué lenguaje está hecho Laravel?',
                    'opciones' => [
                        ['texto' => 'Python', 'correcta' => false],
                        ['texto' => 'PHP', 'correcta' => true],
                        ['texto' => 'Java', 'correcta' => false],
                    ],
                    'explicacion' => 'Laravel es un framework de PHP.',
                ],
                [
                    'pregunta' => '¿Qué sistema de plantillas usa Laravel en sus vistas?',
                    'opciones' => [
                        ['texto' => 'Blade', 'correcta' => true],
                        ['texto' => 'Twig', 'correcta' => false],
                        ['texto' => 'Handlebars', 'correcta' => false],
                    ],
                    'explicacion' => 'Blade son las plantillas .blade.php.',
                ],
            ],
            'base-datos' => [
                [
                    'pregunta' => '¿Qué gestor de base de datos usa nuestro proyecto?',
                    'opciones' => [
                        ['texto' => 'MySQL', 'correcta' => false],
                        ['texto' => 'PostgreSQL', 'correcta' => true],
                        ['texto' => 'SQLite', 'correcta' => false],
                    ],
                    'explicacion' => 'Usamos PostgreSQL.',
                ],
                [
                    'pregunta' => '¿Qué comando de Laravel crea las tablas desde las migraciones?',
                    'opciones' => [
                        ['texto' => 'php artisan migrate', 'correcta' => true],
                        ['texto' => 'php artisan serve', 'correcta' => false],
                        ['texto' => 'composer install', 'correcta' => false],
                    ],
                    'explicacion' => 'migrate ejecuta las migraciones pendientes.',
                ],
            ],
            'proyecto-1' => [
                [
                    'pregunta' => '¿Qué estados puede tener un puesto del mercado?',
                    'opciones' => [
                        ['texto' => 'Libre, ocupado y clausurado', 'correcta' => true],
                        ['texto' => 'Abierto y cerrado', 'correcta' => false],
                        ['texto' => 'Activo y pausado', 'correcta' => false],
                    ],
                    'explicacion' => 'Así los modelamos en la tabla puestos.',
                ],
                [
                    'pregunta' => '¿Cómo se calcula el importe de una obligación?',
                    'opciones' => [
                        ['texto' => 'Superficie × tarifa de la sección', 'correcta' => true],
                        ['texto' => 'Número de pagos × 10', 'correcta' => false],
                        ['texto' => 'Un monto fijo', 'correcta' => false],
                    ],
                    'explicacion' => 'importe = superficie_m2 × tarifa_m2.',
                ],
            ],
            'proyecto-2' => [
                [
                    'pregunta' => '¿Qué perfil puede registrar pagos pero no crear ni eliminar puestos?',
                    'opciones' => [
                        ['texto' => 'Visitante', 'correcta' => false],
                        ['texto' => 'Cobrador', 'correcta' => true],
                        ['texto' => 'Admin', 'correcta' => false],
                    ],
                    'explicacion' => 'El cobrador cobra y consulta; el admin administra.',
                ],
                [
                    'pregunta' => '¿Cómo se ve el código de un puesto de la sección Verduras?',
                    'opciones' => [
                        ['texto' => 'VER-001', 'correcta' => true],
                        ['texto' => '001-VER', 'correcta' => false],
                        ['texto' => 'VERDURAS1', 'correcta' => false],
                    ],
                    'explicacion' => 'Prefijo de 3 letras de la sección + correlativo de 3 dígitos.',
                ],
            ],
        ];

        return view('ruleta.index', compact('categorias', 'preguntas'));
    }
}
