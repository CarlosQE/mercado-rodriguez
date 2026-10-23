# Arquitectura

Aplicación Laravel con Blade en el servidor: un controlador por módulo, validación en Form Requests y operaciones críticas en transacciones. No hay API ni registro público; el acceso se resuelve con sesiones y roles.

## Estructura

```
app/Http/Controllers/   Seccion, Puesto, Comerciante, Asignacion, Obligacion, Pago,
                        Login, Inicio, Mapa, Dashboard, DatoMercado, FotoMercado
app/Http/Middleware/    VerificarRol (alias `rol`)
app/Http/Requests/      MarketRequest (base) y Save* de cada módulo
app/Models/             9 modelos, incluido User
resources/views/        Layout/layout + una carpeta por módulo, admin/ y auth/
tests/Feature/          Autorización, mercado, dashboard, mapa y módulos existentes
```

## Acceso y permisos

- `users.rol` guarda `admin` o `cobrador` (por defecto `cobrador`). `User::tieneRol(...)` centraliza la comprobación.
- El middleware `VerificarRol` se registra con el alias `rol` en `bootstrap/app.php` y acepta una lista: `rol:admin` o `rol:admin,cobrador`.
- Las rutas se agrupan con `auth` y `rol`; además, las vistas ocultan los botones de escritura a quien no tiene permiso.
- Visitante: portada, puestos libres y login. Cobrador: lecturas, mapa, fichas y registrar pagos. Admin: todo.
- Las rutas `create` se registran antes que las rutas con parámetro o se restringen con `whereNumber` para que `puestos/create` no caiga en `puestos/{puesto}`.
- El login valida en el controlador, regenera la sesión y usa `throttle:6,1`; el cierre de sesión es un `POST` y el admin va al dashboard, el cobrador al mapa.

## Rutas

| Ámbito | Rutas |
|---|---|
| Público | `GET /` (`inicio`), `GET /puestos-libres` (`puestos.libres`), `GET /login`, `POST /login` |
| Autenticado (admin y cobrador) | `GET /mapa`, `secciones.index`, `puestos.index`, `puestos.show`, `comerciantes.index`, `asignaciones.index`, `obligaciones.index`, `pagos.index`, `pagos.show`, `pagos.create`, `pagos.store`, `POST /logout` |
| Solo admin | `GET /dashboard`; CRUD de secciones, puestos y comerciantes; `asignaciones.create`, `asignaciones.store`, `PATCH /asignaciones/{asignacion}/cerrar`; `POST /obligaciones/emitir`; `GET/PUT /admin/mercado`; `admin/fotos.index/store/update/destroy` |

Asignaciones y pagos no se editan ni se eliminan: son historial.

## Flujos transaccionales

**Asignar un puesto.** En una transacción se bloquea el puesto (`lockForUpdate`), se comprueba que siga `libre`, se crea la asignación vigente y el puesto pasa a `ocupado`. El formulario ignora `fecha_fin`.

**Cerrar una asignación.** Se fija `fecha_fin` (no anterior al inicio ni posterior a hoy) y el puesto vuelve a `libre`.

**Emitir obligaciones.** Se factura a quien tenga el puesto el primer día del período (`fecha_inicio <= día 1` y `fecha_fin` nula o posterior). Se omiten las asignaciones que ya tienen obligación en ese período, no se emiten períodos futuros y el vencimiento por defecto es el día 10. Cada obligación copia superficie y tarifa e importa `superficie × tarifa`.

**Registrar un pago.** En una transacción: se toma un bloqueo consultivo (`pg_advisory_xact_lock`) para numerar sin repetir, se bloquean las obligaciones elegidas y se verifica que sigan pendientes, se genera el comprobante siguiente al mayor existente, se crea el pago con `total` igual a la suma de importes, se llena `pago_obligacion` y las obligaciones pasan a `pagada`. No hay pagos parciales.

**Dashboard.** Las cifras se calculan con agregaciones (`count`, `sum`, `groupBy`) y colecciones de PHP. La ocupación por sección usa `withCount`; el mapa carga `secciones.puestos.asignacionVigente.comerciante` en pocas consultas para evitar N+1. La recaudación de seis meses se agrupa por año y mes con `extract` de PostgreSQL.

**Mapa y ficha.** El mapa muestra cada puesto con colores por estado y el titular vigente en el atributo `title`. La ficha carga la sección, la asignación vigente, el historial completo y las últimas obligaciones del puesto.

## Validaciones

| Request | Reglas principales |
|---|---|
| SaveSeccionRequest | `nombre` requerido, máx. 80, único; `tarifa_m2` entre 0.01 y 9999.99 |
| SavePuestoRequest | `seccion_id` existente; `codigo` máx. 30, `[A-Z0-9-]+`, único; `superficie_m2` mayor que 0, hasta 2 decimales, máx. 999999.99 |
| SaveComercianteRequest | `documento` máx. 30, único; nombres y apellidos máx. 100; `rubro` máx. 80; teléfono con formato; email opcional; dirección; `activo` booleano |
| SaveAsignacionRequest | Puesto existente; comerciante activo; fechas `Y-m-d`; `fecha_fin` posterior al inicio; motivo; **sin superposición de intervalos** en el mismo puesto |
| SavePagoRequest | Al menos una obligación, todas pendientes y del comerciante; fecha no futura; método efectivo o transferencia |
| SaveDatoMercadoRequest | Nombre, historia, dirección, coordenadas, horario y teléfono obligatorios; eslogan, referencia y email opcionales |
| SaveFotoMercadoRequest | Título y orden obligatorios; imagen JPG/PNG/WebP de hasta 4 MB, obligatoria al crear y opcional al editar |

Validaciones dentro de controladores: el estado de un puesto no ocupado solo puede ser `libre` o `clausurado`; al emitir, el período es `AAAA-MM` y no futuro.

Mensajes: `ok` (aviso neutro) y `error` (aviso durazno) desde el layout; los errores de campo se muestran junto a cada campo.
