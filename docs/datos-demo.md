# Seeders y factories

## Cómo cargar los datos

La primera preparación sobre una base vacía se realiza con `./scripts/setup --demo`. Si las migraciones ya están aplicadas y las tablas de demo siguen vacías, ejecutar `php artisan db:seed`.

`DatabaseSeeder` inserta las cuatro secciones con `insertOrIgnore` y luego llama a `MercadoDemoSeeder`. El catálogo puede ejecutarse sin duplicar nombres, pero la siembra completa **no es idempotente**: el demo rechaza puestos, comerciantes o tarifas existentes. No utilizar una segunda siembra como actualización de información.

`MercadoDemoSeeder` crea su conjunto dentro de una transacción y usa semilla Faker `20261017`. Las fechas dependen del día de ejecución y el contenido puede depender de la versión de Faker; el lock fija las dependencias. No se prometen IDs consecutivos o iguales entre ejecuciones.

## Composición

| Entidad o situación | Cantidad |
|---|---:|
| Secciones | 4 |
| Puestos | 300 |
| Comerciantes ficticios | 280 |
| Asignaciones totales | 280 |
| Asignaciones actuales | 260 |
| Transferencias | 20 |
| Puestos libres | 40 |
| Puestos ocupados | 250 |
| Puestos clausurados con titular | 10 |
| Versiones de tarifa | 8 |
| Obligaciones | 1560 |
| Pagos | 1300 |
| Inspecciones | 520 |
| Hallazgos | 100 |

Los 280 comerciantes son 260 titulares actuales y 20 anteriores. Hay 260 puestos con titular vigente; diez de ellos están clausurados, por lo que el estado final ocupado corresponde a 250. El resto de los puestos está libre y no genera mensualidades en el demo.

## Distribución y tarifas de ejemplo

| Sección | Código | Puestos | Precio anterior/m² | Precio actual/m² | Intervalo sanitario recomendado |
|---|---|---:|---:|---:|---:|
| Carnes | CAR-001…CAR-080 | 80 | 30.0000 | 32.0000 | 30 días |
| Verduras | VER-001…VER-100 | 100 | 18.0000 | 20.0000 | 30 días |
| Abarrotes | ABA-001…ABA-080 | 80 | 24.0000 | 26.0000 | 60 días |
| Comida | COM-001…COM-040 | 40 | 35.0000 | 37.0000 | 15 días |

Son tarifas ficticias, expresadas bajo la propuesta de moneda BOB. Comida usa superficies de 9, 12, 15 o 18 m²; las otras secciones usan 4, 6, 7.5 u 8 m². No se establece un límite funcional de 300 puestos.

La historia comienza el primer día de hace cinco meses; las transferencias y el cambio de tarifa ocurren el primer día de hace dos meses. Se crean seis mensualidades, incluida la actual, con vencimiento el día 10. El 17/10/2026 esto corresponde a mayo–octubre y cambio de titular/tarifa el 01/08/2026. No confundir estos vencimientos del demo con una regla municipal confirmada.

Los patrones por comerciante generan pagos completos, saldos parciales y períodos impagados. La fecha de un pago se limita al día de ejecución para no crear pagos futuros. Los recibos se identifican por mes e índice de titular. Cada puesto asignado tiene dos visitas; unas tienen próxima revisión vencida y otras vigente. Los puestos clausurados tienen una visita reciente no aprobada. Los hallazgos antiguos se marcan subsanados y los recientes quedan abiertos. No se crean recibos anulados en el demo: ese escenario lo cubren las pruebas.

Nombres, apellidos y direcciones siguen formatos habituales de La Paz; los teléfonos y documentos son ficticios. No representan un padrón real ni una verificación de identidad.

## Factories disponibles

Cada modelo tiene su factory en `database/factories` y usa `HasFactory`. Las relaciones no suministradas generan sus propios registros. `ObligacionFactory` resuelve la tarifa para la sección de la asignación generada; `PagoFactory` obtiene el importe de la obligación. `HallazgoFactory` crea por defecto una inspección con observaciones.

Ejemplo PHP para una prueba que ya haya arrancado Laravel y configurado una base/esquema de pruebas:

```php
use App\Models\Comerciante;
use App\Models\Obligacion;

$comerciantes = Comerciante::factory()->count(5)->create();
$obligacion = Obligacion::factory()->create()->refresh();
```

Esto no es un comando de consola: el proyecto no incluye Tinker como dependencia. Usar el arranque de `tests/runtime/check.php` como referencia para pruebas nuevas.

Sobrescribir IDs requiere revisar todas las referencias y fechas. Una factory aislada puede crear una sección nueva; para poblar el mercado completo usar el seeder, que distribuye las cuatro secciones. No usar factories como lógica de producción ni como un mecanismo para saltarse restricciones SQL.
