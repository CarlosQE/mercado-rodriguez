# Arquitectura y mapa del código

## Responsabilidades actuales

El diseño separa datos persistentes, relaciones de dominio y validación de entradas. Las restricciones de PostgreSQL protegen también las escrituras realizadas fuera de HTTP. Los casos de uso transaccionales todavía no tienen implementación propia.

| Ubicación | Responsabilidad |
|---|---|
| `bootstrap/app.php` | Arranque Laravel, comandos de consola y ruta de salud `/up` |
| `bootstrap/providers.php` | Lista vacía de proveedores propios |
| `public/index.php` | Entrada HTTP; no contiene lógica de negocio |
| `routes/console.php` | Espacio reservado; no hay emisor mensual ni tareas programadas |
| `config/` | Configuración mínima de aplicación, PostgreSQL y logging |
| `app/Models/` | Modelos Eloquent, campos asignables, casts y relaciones |
| `app/Http/Requests/` | Validación de datos y requisito básico de usuario autenticado |
| `database/schema/mercado.sql` | Esquema inicial PostgreSQL, vista, funciones y triggers |
| `database/migrations/` | Evolución ordenada del esquema; hoy dos migraciones |
| `database/factories/` | Construcción de escenarios y relaciones compatibles |
| `database/seeders/` | Catálogos y demostración completa |
| `tests/runtime/check.php` | Integración con PostgreSQL y prueba concurrente |
| `tests/sql/demo_assertions.sql` | Comprobaciones de la siembra, sin modificar filas |
| `scripts/`, `compose.yaml` | Preparación, servidor local y base de desarrollo |

No hay controladores, vistas propias, rutas web/API de negocio, modelo User, migración de usuarios, políticas, repositorios, servicios, jobs ni configuración de frontend. La base Laravel es mínima: no asumir que todos los archivos del esqueleto estándar están presentes.

## Relaciones Eloquent

Todas las entidades usan claves `id`, tablas explícitas en español, timestamps y `HasFactory`.

| Modelo | Relaciones disponibles |
|---|---|
| `Seccion` | `puestos()`, `tarifas()` |
| `Comerciante` | `asignaciones()` |
| `Puesto` | `seccion()`, `asignaciones()`, `clausuras()` |
| `Tarifa` | `seccion()`, `obligaciones()` |
| `Asignacion` | `puesto()`, `comerciante()`, `obligaciones()`, `inspecciones()` |
| `Clausura` | `puesto()` |
| `Obligacion` | `asignacion()`, `tarifa()`, `pagos()` |
| `Pago` | `obligacion()` |
| `Inspeccion` | `asignacion()`, `hallazgos()` |
| `Hallazgo` | `inspeccion()` |

`Puesto::estadoEn(string $fecha)` recibe una fecha `Y-m-d` y consulta clausura y asignación vigentes; devuelve `clausurado`, `ocupado` o `libre`, en ese orden de prioridad. No es una columna ni un accesor añadido automáticamente al JSON.

`Puesto::ultimaInspeccion()` devuelve `?Inspeccion`: consulta todas las asignaciones del puesto y ordena por fecha e ID descendentes. No es una relación Eloquent susceptible de `with()`. Para listados masivos, planificar una consulta agregada y evitar llamar estos métodos por cada fila sin medir el número de consultas.

Los casts `date`/`datetime` entregan objetos de fecha; los casts `decimal:2`/`decimal:4` conservan representación decimal como cadena. La vista `saldos_obligaciones` se consulta con Query Builder; no existe un modelo propio para ella.

## Persistencia y evolución

La migración inicial carga `database/schema/mercado.sql` mediante `DB::unprepared`. No es un dump para `schema:load`. La segunda migración agrega `pagos_anulacion_motivo_check`: un motivo de anulación debe ser explícitamente no nulo y no vacío. El CHECK inicial por sí solo no bastaba debido a la lógica de NULL de PostgreSQL.

Después de aplicar una migración, conservarla y añadir otra para modificar el esquema. Actualizar el diccionario y las pruebas cuando cambien columnas o funciones. No sustituir PostgreSQL por SQLite en pruebas: se usan rangos, EXCLUDE/GiST, triggers, columnas generadas y bloqueos reales.

La transacción de una transferencia deberá cerrar la asignación previa y crear la siguiente. La emisión deberá elegir asignación y tarifa vigentes al inicio del mes. Un Request comprueba entradas, pero no coordina operaciones ni sustituye bloqueos. El trigger de pago bloquea la obligación con `FOR UPDATE`; el contrato probado usa READ COMMITTED.

## Aplicación de KISS y SOLID

Mantener los modelos centrados en persistencia y relaciones, Requests en validación y futuros casos de uso en la coordinación transaccional. Cuando existan controladores, deben delegar esas operaciones. Extraer una abstracción cuando haya una necesidad concreta de sustitución o reutilización; no crear repositorios genéricos o interfaces sin consumidores.

Las reglas compartidas deben tener un lugar claro y pruebas: una restricción SQL garantiza integridad; una validación equivalente permite explicar el error antes de escribir. Las dos capas deben evolucionar juntas. `$fillable` no concede autorización ni garantiza que un cambio histórico sea válido.

## Límites del historial actual

Las obligaciones guardan superficie y tarifa al emitir; asignaciones e inspecciones conservan la referencia al titular de ese momento. Eso no constituye una auditoría completa de cada atributo: cambiar nombres o dirección del comerciante modifica lo que muestran futuras consultas por esa FK. No hay snapshots de identidad, usuario autor de las operaciones ni bitácora antes/después.

Los triggers bloquean DELETE de asignaciones, tarifas, obligaciones, pagos, inspecciones, hallazgos y puestos. Secciones y comerciantes quedan protegidos de borrado cuando tienen referencias. Las clausuras tienen control de intervalos y FK, pero todavía no un trigger propio de inmutabilidad/borrado; las futuras acciones deberán definir su política de corrección. Tampoco hay un control de privilegios que impida a un administrador usar TRUNCATE o desactivar protecciones. Son límites a considerar al diseñar permisos y auditoría.
