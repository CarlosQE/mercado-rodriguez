# Pruebas

```bash
php artisan tinker --execute="DB::statement('CREATE DATABASE mercado_rodriguez_test');"   # una sola vez
composer test
```

Ejecuta PHPUnit sobre `tests/Feature` contra la base `mercado_rodriguez_test` (forzada en `phpunit.xml`). Cada prueba usa `RefreshDatabase`, así que esa base se reinicia. `tests/TestCase.php` aborta si la base activa no termina en `_test`, para que un error de configuración no pueda vaciar los datos de desarrollo.

## Qué cubren

| Archivo | Comportamiento verificado |
|---|---|
| `AsignacionTest` | Crear una asignación ocupa el puesto; se rechazan intervalos superpuestos y comerciantes inactivos; cerrar una asignación libera el puesto |
| `ObligacionTest` | Emitir calcula `superficie × tarifa`, fija vencimiento el día 10 y no duplica; no factura asignaciones cerradas antes del período; rechaza períodos futuros |
| `PagoTest` | Un pago suma los importes y marca las obligaciones como pagadas; comprobantes correlativos; rechaza obligaciones ajenas o ya pagadas |

## Fuera de alcance

Los CRUD de secciones, puestos y comerciantes y la concurrencia entre pagos simultáneos no tienen pruebas automatizadas.
