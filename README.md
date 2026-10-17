# Mercado Rodriguez

Base Laravel 13 / PHP 8.3+ para diseñar los datos de alquileres de un mercado municipal ficticio. PostgreSQL 16+ con extensión `btree_gist`. Sin controladores, vistas ni rutas HTTP.

Ver [diagrama ER, cardinalidades y reglas](docs/entidad-relacion.md) y [esquema SQL](database/schema/mercado.sql).

## Entorno local

PHP 8.3+ con `pdo_pgsql`, Composer y Docker Compose. En este equipo se encontró PHP 8.5.0, Composer 2.8.12 y Laravel Installer 5.32.0 instalados por Herd Lite en `~/.config/herd-lite/bin`; se añadieron accesos en `~/.local/bin`.

```bash
cd /home/CarlosQE/dev/personal/mercado-rodriguez
./scripts/setup --demo
./scripts/serve
```

`setup` instala dependencias, prepara `.env` si no existe, genera la clave de aplicación, inicia PostgreSQL 17 y ejecuta las migraciones. `--demo` añade los datos de prueba y sólo debe usarse sobre las tablas del demo vacías. Para volver a preparar un entorno ya sembrado, ejecutar `./scripts/setup` sin ese argumento.

El archivo `.env` local ya está preparado con una clave y contraseña aleatorias; no se versiona. PostgreSQL escucha sólo en `127.0.0.1:5433` y conserva sus datos en un volumen. `docker compose stop` detiene la base sin borrarla. El usuario de PostgreSQL del contenedor tiene privilegios de administración para desarrollo; no reutilizar esa configuración en producción.

La ruta `http://127.0.0.1:8000/up` comprueba el arranque HTTP de Laravel. No verifica la conexión a la base de datos. La raíz `/` devuelve 404 porque esta sesión no implementa interfaz ni rutas de negocio.

Para usar un PostgreSQL existente, configurar `.env`, ejecutar `composer install`, `php artisan key:generate` sólo si falta la clave, y `php artisan migrate --seed`; no ejecutar `setup`, que inicia el contenedor local. La migración instala `btree_gist`: si el usuario no tiene permisos, debe prepararla un administrador.

La migración ejecuta el esquema SQL versionado dentro de la transacción de PostgreSQL. No modificar ese archivo después de aplicar la migración: los cambios posteriores requieren nuevas migraciones.

## Alcance

10 modelos Eloquent, 10 Form Requests, 10 factories, migración con restricciones, seeders de demostración, vista de saldos y documentación ER. No incluye emisión mensual ejecutable, transferencias transaccionales como caso de uso, envío de alertas, autenticación ni interfaz; se han diseñado los datos que esas operaciones necesitarán. No se añaden capas de repositorios o servicios vacíos.

Para emitir una obligación se envían asignación, tarifa, período y fechas: la base obtiene superficie y precio. Para consultar mora:

```sql
SELECT * FROM saldos_obligaciones
WHERE fecha_vencimiento < CURRENT_DATE AND saldo > 0;
```

## Verificación

Comprobado en este equipo: `composer validate --strict` y revisión de sintaxis PHP. La instalación de dependencias se intentó y falló porque el entorno no puede resolver `repo.packagist.org`. Docker Compose está instalado, pero la sesión no tiene permiso para acceder a su socket. Por eso todavía no se han ejecutado Artisan, migraciones ni seeders; no existe `composer.lock` hasta completar la primera instalación.

Una vez instalado el entorno:

```bash
composer validate --strict
find app bootstrap config database routes public -name '*.php' -exec php -l {} \;
php artisan migrate:status
# Sólo en una base desechable:
php artisan migrate:fresh --seed --env=testing
```

`migrate:fresh` debe ejecutarse exclusivamente en una base de pruebas vacía/desechable. Revisar además los escenarios de [verificación](docs/verificacion.md).

Referencias oficiales: [Laravel 13](https://laravel.com/docs/13.x), [Form Requests](https://laravel.com/docs/13.x/validation#form-request-validation).

## Datos de demostración

`php artisan migrate --seed` crea cuatro secciones, **300 puestos y 280 comerciantes ficticios** con nombres habituales, teléfonos móviles y direcciones de La Paz. Los datos no provienen de personas reales.

- 260 titulares actuales y 20 titulares anteriores; 20 transferencias.
- 40 puestos libres, 250 ocupados y 10 clausurados con titular vigente.
- Ocho versiones de tarifas y 1.560 obligaciones de seis meses, incluido el actual.
- Pagos completos, pagos parciales y obligaciones pendientes; recibos únicos.
- 520 inspecciones, próximas fechas vigentes y vencidas, hallazgos abiertos y subsanados.

El seeder usa semilla aleatoria fija y fechas relativas a la ejecución; se ejecuta en una transacción. Rechaza una base con puestos, comerciantes o tarifas para evitar duplicaciones y mezclas. Para cargar únicamente las secciones, extraer ese catálogo en un seeder propio antes de un despliegue real; el seeder predeterminado es exclusivamente de demostración.

Las factories permiten construir escenarios pequeños, por ejemplo `Comerciante::factory()->count(5)->create()`. Al sobrescribir IDs relacionados, suministrar referencias compatibles con las fechas y sección; las restricciones SQL siguen activas. `Obligacion::factory()->create()->refresh()` recupera los valores calculados por PostgreSQL.

Después de sembrar, ejecutar `psql` con las credenciales de la base de pruebas y `-v ON_ERROR_STOP=1 -f tests/sql/demo_assertions.sql` para comprobar cantidades y coherencia. Este script aún no se ha ejecutado en el entorno de elaboración.

## Historial Git

Repositorio local inicializado en la rama `main`; sin remoto por ahora. `.env`, credenciales de Composer, dependencias y archivos temporales están excluidos. Después de la primera instalación exitosa, versionar `composer.lock` para fijar las versiones utilizadas.
