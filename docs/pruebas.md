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
| `AutorizacionTest` | Visitante redirigido al login; cobrador consulta y paga pero no administra ni abre el dashboard; admin administra; login redirige según el rol |
| `MercadoTest` | Portada y puestos libres públicos; accesorio `url` de las fotos; permisos de ficha/fotos; subida con `Storage::fake('public')` |
| `DashboardTest` | El admin ve los totales correctos sobre datos de prueba; el cobrador recibe 403 |
| `MapaTest` | El cobrador ve el mapa y la ficha del puesto; el visitante es redirigido al login |
| `ComunicadoTest` | Tablero público solo con avisos vigentes; permisos de administración; CRUD y validación de fechas |
| `PuestoTest` | El código se genera como `VER-001`, `VER-002` por sección y no se puede sobrescribir desde `store` ni `update` |

En total son 29 pruebas. Los tests existentes actúan como admin mediante el auxiliar `usuario()` del trait `CreaMercado`.

## Fuera de alcance

Los CRUD de secciones y comerciantes no tienen pruebas automatizadas; de puestos solo se prueba la generación del código. La concurrencia entre pagos simultáneos tampoco tiene pruebas automatizadas.
