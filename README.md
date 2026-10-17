# Mercado Rodriguez

Base Laravel 13 / PHP 8.3+ para diseñar los datos de alquileres de un mercado municipal ficticio. PostgreSQL 16+ con extensión `btree_gist`. Sin controladores ni vistas de negocio; sólo una ruta HTTP de salud.

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

Ejecutado el 17 de octubre de 2026 con PHP 8.5.0, Laravel 13.37.0 y PostgreSQL 17:

- Dependencias instaladas y fijadas en `composer.lock`; resolución configurada para PHP 8.3 como versión mínima. La ejecución comprobada fue con PHP 8.5.
- Migraciones y seeder ejecutados: 300 puestos, 280 comerciantes, 1.560 obligaciones, 1.300 pagos, 520 inspecciones y 100 hallazgos.
- Validación de cantidades y coherencia mediante `tests/sql/demo_assertions.sql`.
- Pruebas de integración reales: migración/rollback, restricciones, transferencias, estados, inspecciones, pagos/anulaciones, Form Requests y pagos simultáneos.
- `/up` respondió HTTP 200; `/` respondió 404 como corresponde al alcance actual.
- `composer validate --strict` y `composer check-platform-reqs` correctos. Composer no reportó avisos de vulnerabilidades en la actualización de dependencias.

Para repetir las pruebas con PostgreSQL iniciado:

```bash
composer test
docker compose exec -T postgres sh -c 'psql -U "$POSTGRES_USER" -d "$POSTGRES_DB" -v ON_ERROR_STOP=1' < tests/sql/demo_assertions.sql
```

`composer test` crea y elimina un esquema temporal aislado en la base configurada; necesita permiso CREATE SCHEMA. No borra ni vuelve a sembrar los datos de demostración. Usa procesos PHP separados para verificar el bloqueo de pagos concurrentes. El script SQL comprueba la siembra de demostración sin modificar filas. Los escenarios complementarios se describen en [verificación](docs/verificacion.md).

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

Después de sembrar, ejecutar `psql` con las credenciales de la base de pruebas y `-v ON_ERROR_STOP=1 -f tests/sql/demo_assertions.sql` para comprobar cantidades y coherencia. El script fue ejecutado correctamente.

## Historial Git

Repositorio local inicializado en la rama `main`; sin remoto por ahora. `.env`, credenciales de Composer, dependencias y archivos temporales están excluidos. `composer.lock` está versionado para fijar las versiones utilizadas.
