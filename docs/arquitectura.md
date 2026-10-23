# Arquitectura

Aplicación Laravel con Blade en el servidor: un controlador por módulo, validación en Form Requests y operaciones críticas en transacciones. Sin API ni autenticación.

## Estructura

```
app/Http/Controllers/   Seccion, Puesto, Comerciante, Asignacion, Obligacion, Pago
app/Http/Requests/      MarketRequest (base) y Save* de cada módulo
app/Models/             6 modelos
resources/views/        Layout/layout + una carpeta por módulo
tests/Feature/          Pruebas de asignaciones, obligaciones y pagos
```

## Rutas

| Módulo | Rutas |
|---|---|
| Secciones, Puestos, Comerciantes | CRUD completo sin `show` |
| Asignaciones | `index`, `create`, `store`, `PATCH /asignaciones/{asignacion}/cerrar` |
| Obligaciones | `index`, `POST /obligaciones/emitir` |
| Pagos | `index`, `create`, `store`, `show` |
| `/` | Redirige a `/secciones` |

Asignaciones y pagos no se editan ni se eliminan: son historial.

## Flujos transaccionales

**Asignar un puesto.** En una transacción se bloquea el puesto (`lockForUpdate`), se comprueba que siga `libre`, se crea la asignación vigente y el puesto pasa a `ocupado`. El formulario ignora `fecha_fin`.

**Cerrar una asignación.** Se fija `fecha_fin` (no anterior al inicio ni posterior a hoy) y el puesto vuelve a `libre`.

**Emitir obligaciones.** Se factura a quien tenga el puesto el primer día del período (`fecha_inicio <= día 1` y `fecha_fin` nula o posterior). Se omiten las asignaciones que ya tienen obligación en ese período, no se emiten períodos futuros y el vencimiento por defecto es el día 10. Cada obligación copia superficie y tarifa e importa `superficie × tarifa`.

**Registrar un pago.** En una transacción: se toma un bloqueo consultivo (`pg_advisory_xact_lock`) para numerar sin repetir, se bloquean las obligaciones elegidas y se verifica que sigan pendientes, se genera el comprobante siguiente al mayor existente, se crea el pago con `total` igual a la suma de importes, se llena `pago_obligacion` y las obligaciones pasan a `pagada`. No hay pagos parciales.

## Validaciones

| Request | Reglas principales |
|---|---|
| SaveSeccionRequest | `nombre` requerido, máx. 80, único; `tarifa_m2` entre 0.01 y 9999.99 |
| SavePuestoRequest | `seccion_id` existente; `codigo` máx. 30, `[A-Z0-9-]+`, único; `superficie_m2` mayor que 0, hasta 2 decimales, máx. 999999.99 |
| SaveComercianteRequest | `documento` máx. 30, único; nombres y apellidos máx. 100; `rubro` máx. 80; teléfono con formato; email opcional; dirección; `activo` booleano |
| SaveAsignacionRequest | Puesto existente; comerciante activo; fechas `Y-m-d`; `fecha_fin` posterior al inicio; motivo; **sin superposición de intervalos** en el mismo puesto |
| SavePagoRequest | Al menos una obligación, todas pendientes y del comerciante; fecha no futura; método efectivo o transferencia |

Validaciones dentro de controladores: el estado de un puesto no ocupado solo puede ser `libre` o `clausurado`; al emitir, el período es `AAAA-MM` y no futuro.

Mensajes: `ok` (aviso neutro) y `error` (aviso durazno) desde el layout; los errores de campo se muestran junto a cada campo.
