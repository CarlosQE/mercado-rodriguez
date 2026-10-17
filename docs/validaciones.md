# Contratos de entrada y validación

Estas clases existen en `app/Http/Requests`; todavía no hay endpoints que las utilicen. Los ejemplos representan entradas de los Requests, no una API publicada. Todos los IDs son referencias a registros existentes.

## Contrato común

`MarketRequest::authorize()` exige que `$this->user()` no sea nulo. No verifica roles ni políticas. Falta implementar usuarios, guardas, middleware y permisos antes de exponer operaciones.

Los `Save*Request` exigen la representación completa para crear o reemplazar datos: no implementan un PATCH parcial. Las fechas deben tener formato `Y-m-d`. `nullable` permite omitir o enviar nulo; no significa que un campo obligatorio pueda reemplazarse por texto vacío. Los límites indicados abajo corresponden al Request; consultar el diccionario para distinguir las garantías SQL.

En las ediciones admitidas usar model binding con parámetros `seccion`, `comerciante`, `puesto`, `tarifa`, `asignacion` y `clausura`. La exclusión del propio registro usa el parámetro de ruta; no sustituirlo por un ID que venga en el cuerpo. No enviar el cuerpo entero a `create()`/`update()`; seleccionar `validated()` y los campos permitidos para la acción concreta.

## Matriz de reglas

| Request | Campos y reglas |
|---|---|
| `SaveSeccionRequest` | `nombre` obligatorio, hasta 80 y único; `intervalo_inspeccion_dias` entero entre 1 y 3650 |
| `SaveComercianteRequest` | `tipo_documento`: CI/PASAPORTE/NIT; `documento` hasta 30, único junto al tipo; nombres/apellidos hasta 100; teléfono de 6 a 25 caracteres con dígitos, +, espacios, paréntesis o guion; dirección hasta 255; email válido opcional hasta 254; activo booleano opcional |
| `SavePuestoRequest` | Sección existente; código único de hasta 30, sólo A–Z, 0–9 y guion; superficie positiva con hasta 2 decimales y máximo 99999999.99 |
| `SaveTarifaRequest` | Sección existente; precio positivo con hasta 4 decimales y máximo 99999999.9999; inicio obligatorio y fin opcional posterior; `after()` rechaza superposición en esa sección |
| `SaveAsignacionRequest` | Puesto existente y comerciante activo; inicio obligatorio y fin opcional posterior; motivo obligatorio hasta 255; `after()` rechaza asignaciones superpuestas del puesto |
| `SaveClausuraRequest` | Puesto existente, inicio y fin opcional posterior; motivo obligatorio hasta 5000; `after()` rechaza clausuras superpuestas |
| `SaveObligacionRequest` | Asignación y tarifa existentes; período primer día del mes y único por asignación; emisión/vencimiento obligatorios y vencimiento no anterior a emisión; `after()` verifica vigencia de ambas referencias y sección correspondiente |
| `SavePagoRequest` | Obligación existente; recibo único hasta 50; fecha no futura; importe positivo, hasta 2 decimales y máximo 9999999999999999.99; método efectivo/transferencia; referencia opcional hasta 100 y obligatoria para transferencia; `after()` comprueba saldo |
| `SaveInspeccionRequest` | Asignación existente; fecha no futura y dentro de su vigencia; inspector obligatorio hasta 150; resultado aprobada/con_observaciones/no_aprobada; próxima fecha posterior; observaciones opcionales hasta 5000 |
| `SaveHallazgoRequest` | Inspección existente; descripción hasta 5000; fecha límite no anterior a visita; subsanación opcional entre visita y hoy; evidencia textual hasta 5000, obligatoria al subsanar |

## Campos reservados y acciones históricas

La obligación prohíbe recibir `superficie_m2`, `monto_m2` e `importe`. El trigger toma los dos primeros del puesto y la tarifa y PostgreSQL genera el total. Aunque superficie y precio aparecen en `$fillable`, el trigger los reemplaza al insertar. Tras crear una obligación, usar `refresh()` para leer los valores calculados.

El pago prohíbe recibir `anulado_en` y `motivo_anulacion`. Además, esos campos no están en su `$fillable`. No existe todavía un Request de anulación; deberá crearse una acción específica, con política, fecha y motivo, en lugar de reutilizar el alta de pago.

Tarifas: una versión existente sólo permite cambiar el fin de vigencia respetando sus obligaciones; para cambiar precio, crear versión nueva. Asignaciones: no cambiar puesto, comerciante ni inicio; sólo cerrar de forma compatible con los documentos existentes. Obligaciones e inspecciones no permiten edición genérica. Hallazgos permiten registrar la subsanación/evidencia; no cambiar visita, descripción o plazo de un hallazgo ya creado.

Clausuras no tienen aún una política completa de edición histórica; ver [límites de arquitectura](arquitectura.md#límites-del-historial-actual).

## Ejemplos de datos

Alta de puesto:

```json
{"seccion_id": 1, "codigo": "CAR-081", "superficie_m2": "6.50"}
```

Emisión de obligación, siempre que las referencias estén vigentes el 1 de octubre:

```json
{"asignacion_id": 1, "tarifa_id": 2, "periodo": "2026-10-01", "fecha_emision": "2026-10-01", "fecha_vencimiento": "2026-10-10"}
```

Pago parcial, si el saldo lo permite:

```json
{"obligacion_id": 1, "recibo": "MR-202610-PRUEBA", "fecha_pago": "2026-10-17", "importe": "50.00", "metodo": "transferencia", "referencia": "BANCO-OPERACION-001"}
```

Las fechas y los IDs son ilustrativos; no garantizan aceptación en otra siembra. Evidencia es texto, no un archivo adjunto; no existe almacenamiento de comprobantes digitales.

## Manejo futuro de errores y límites

Los `after()` sólo consultan la base cuando las reglas iniciales no tienen errores. Hay verificaciones que continúan en SQL: por ejemplo, pago no anterior a emisión, invariantes históricas y concurrencia. Por ello, pasar un Request no garantiza que la escritura posterior sea aceptada. La comprobación de saldo en PHP no sustituye el cálculo decimal y bloqueo en PostgreSQL.

| SQLSTATE | Significado dentro del esquema |
|---|---|
| `23505` | Clave única repetida |
| `23503` | Referencia inexistente o borrado referenciado |
| `23514` | CHECK incumplido |
| `23P01` | Intervalos superpuestos |
| `P0001` | Excepción de regla de negocio emitida por un trigger |
| `40P01` | Deadlock; coordinar reintento de la transacción completa |

La traducción de estas excepciones a respuestas HTTP comprensibles todavía está pendiente. No devolver SQL ni trazas al usuario final. No se ha creado un contrato definitivo de códigos HTTP o estructura JSON para la futura API.
