# Mercado Rodriguez

Base Laravel 13 / PHP 8.3+ para diseñar los datos de alquileres de un mercado municipal ficticio. PostgreSQL 16+ con extensión `btree_gist`. Sin controladores, vistas ni rutas HTTP.

Ver [diagrama ER, cardinalidades y reglas](docs/entidad-relacion.md) y [esquema SQL](database/schema/mercado.sql).

## Instalación

Requisitos: PHP con extensiones requeridas por Laravel y `pdo_pgsql`, Composer, PostgreSQL. Crear una base vacía y un usuario con permisos de migración. Si ese usuario no puede instalar extensiones, un administrador debe ejecutar antes `CREATE EXTENSION btree_gist` en esa base.

```bash
composer install
cp .env.example .env
# Configurar las credenciales de PostgreSQL en .env
php artisan key:generate
php artisan migrate --seed
```

La migración ejecuta el esquema SQL versionado dentro de la transacción de PostgreSQL. No usar ese archivo como un schema dump de Artisan ni modificarlo después de aplicar la migración: los cambios posteriores corresponden a nuevas migraciones.

## Alcance

10 modelos Eloquent, 10 Form Requests, 10 factories, migración con restricciones, seeders de demostración, vista de saldos y documentación ER. No incluye emisión mensual ejecutable, transferencias transaccionales como caso de uso, envío de alertas, autenticación ni interfaz; se han diseñado los datos que esas operaciones necesitarán. No se añaden capas de repositorios o servicios vacíos.

Para emitir una obligación se envían asignación, tarifa, período y fechas: la base obtiene superficie y precio. Para consultar mora:

```sql
SELECT * FROM saldos_obligaciones
WHERE fecha_vencimiento < CURRENT_DATE AND saldo > 0;
```

## Verificación

El entorno de elaboración carece de PHP y Composer, no permite acceso a Docker y no puede resolver github.com. No se ejecutaron Laravel ni migraciones y no hay dependencias instaladas ni composer.lock. La revisión estática no sustituye estas comprobaciones en un entorno con PHP/PostgreSQL:

```bash
composer validate --strict
find app bootstrap config database routes -name '*.php' -exec php -l {} \;
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
