# Continuidad del desarrollo y colaboración

## Punto de partida

La base de datos y sus pruebas están implementadas. No hay operaciones accesibles para un usuario final. Los ejemplos siguientes describen cómo continuar, sin añadir aún controladores o vistas a esta entrega.

Antes de modificar el esquema, leer [reglas y ER](entidad-relacion.md), [diccionario](diccionario-datos.md) y [contratos](validaciones.md). Usar una rama local y conservar el historial de migraciones.

## Decisiones a confirmar con el responsable funcional

| Tema | Supuesto actual / cuestión pendiente |
|---|---|
| Moneda | BOB como propuesta; no hay columna multimoneda |
| Cobro de altas a mitad de mes | Se cobra sólo al titular del día 1, sin prorrateo; acordar si hay que cambiarlo |
| Vencimiento | El demo usa día 10; no existe regla automática de emisión ni plazo confirmado |
| Tarifa | Por sección y vigencia; confirmar si habrá excepciones por puesto |
| Clausura | Conserva titular y no exime de renta; confirmar política de cobro y reapertura |
| Inspección inicial | Definir plazo del puesto que todavía no tiene visitas |
| Transferencia con deuda | La deuda queda en el titular anterior; definir si la mora impide autorizar el traspaso |
| Corrección de documentos | No hay notas de crédito, cancelación de obligaciones ni rectificación de inspecciones |
| Uso del puesto | Un comerciante puede tener varias asignaciones en distintos puestos; no hay límite reglamentario |
| Identidad y auditoría | Definir usuarios, roles, inspectores como entidad y trazabilidad de cada cambio |

Los supuestos están reflejados en el diseño; no deben presentarse como reglas municipales confirmadas. Cambiarlos puede requerir nuevas migraciones y pruebas, especialmente prorrateos, rectificaciones o remodelación física de puestos.

## Secuencia sugerida de implementación

1. Confirmar reglas pendientes y acordar permisos: administración, cobros e inspección son áreas posibles, no roles ya implementados.
2. Diseñar autenticación, usuarios y políticas. Los Requests sólo exigen usuario presente; no hay sistema de acceso completo.
3. Crear casos de uso transaccionales: asignar/transferir, cerrar/abrir vigencias, emitir obligaciones, cobrar/anular pagos, inspeccionar y subsanar. Añadir Requests específicos de cierre/anulación según la acción.
4. Implementar emisión mensual explícita e idempotente antes de programarla: período elegido, tarifa y titular vigentes, vencimiento confirmado y manejo de huecos de tarifa. La unicidad existente protege duplicados, pero no sustituye el proceso de emisión.
5. Incorporar consultas de saldos, mora y revisiones sanitarias; manejar puestos sin inspección. Preparar consultas masivas para evitar N+1.
6. Diseñar rutas, controladores, respuestas HTTP y traducción de errores de PostgreSQL. Después construir vistas o cliente web sobre esos contratos.
7. Añadir auditoría y documentación de operación, pruebas HTTP/roles, automatización CI y despliegue cuando exista un destino definido.

## Guías para los casos de uso pendientes

**Transferencia.** En una transacción, bloquear el puesto y seleccionar la asignación vigente en la fecha elegida. Validar destino y regla de mora; cerrar la asignación anterior y crear otra con inicio en la misma fecha. No cambiar `comerciante_id` en una asignación anterior ni reasignar sus obligaciones. Si la creación falla, debe revertirse también el cierre. No existe hoy un método de servicio que ejecute esa secuencia; un puesto clausurado tampoco se rechaza automáticamente en `SaveAsignacionRequest`.

**Cambio de tarifa.** Cerrar el intervalo anterior y crear la versión nueva de la misma sección de forma atómica. No recalcular obligaciones emitidas. El trigger impide acortar una vigencia dejando obligaciones fuera de ella.

**Emisión.** Usar el primer día del mes; resolver titular y tarifa compatibles; insertar IDs y fechas, sin precio/superficie provenientes del cliente. Si falta tarifa, registrar o devolver un error, sin asumir precio cero. Aplicar `refresh()` al documento recién creado si se necesitan los valores calculados.

**Cobro y anulación.** Trabajar con importes decimales exactos. El trigger serializa pagos sobre la obligación y rechaza sobrepago; la validación previa sólo mejora el mensaje. Una anulación conserva el recibo, exige motivo y sólo puede aplicarse a un pago vigente. El Request de alta no admite esos campos. No existe un servicio de anulación ni un registro del usuario autor.

**Seguimiento sanitario.** Buscar la visita más reciente entre todas las asignaciones del puesto; tomar su `proxima_fecha` sin reiniciarla por una transferencia. El intervalo de sección es una recomendación: actualmente no se calcula automáticamente al guardar una inspección. La inspección no clausura por sí sola. Los hallazgos y la clausura son registros separados, sin FK entre ambos.

## Límites y tareas técnicas identificadas

- El historial de identidad no tiene snapshots ni bitácora; una FK al comerciante conserva la persona vinculada, no cada versión de sus datos.
- Completar la política de inmutabilidad/corrección de clausuras y las reglas de reapertura de asignaciones/tarifas antes de exponer edición histórica.
- Convertir validaciones monetarias futuras a comparaciones decimales exactas; el Request de pago usa actualmente una comparación PHP y la garantía final está en PostgreSQL.
- Cubrir actualizaciones con model binding y carreras adicionales; las pruebas de Requests no recorren middleware/rutas HTTP reales.
- La prueba concurrente cubre READ COMMITTED; no certifica otros aislamientos ni una prueba de carga.
- Separar catálogo de secciones y demo antes de un despliegue real: el seeder predeterminado siempre intenta cargar datos ficticios.
- Reservar una cuenta de migración/pruebas y otra con permisos limitados para la aplicación cuando se prepare producción. El Compose actual es de desarrollo.

## Flujo Git actual

`origin` apunta a `git@github.com:CarlosQE/mercado-rodriguez.git`; el repositorio remoto es público y sigue vacío. Hay commits locales en `main`. **No ejecutar push hasta autorización explícita del responsable.** No crear README, licencia o commits desde GitHub mientras se prepara el primer envío: generaría otro historial que habría que conciliar.

Comandos locales de trabajo:

```bash
git status
git switch -c feature/nombre-del-cambio
# Implementar y revisar el cambio.
composer test
git diff --check
git diff
git add ruta/al/archivo
git commit -m "feat: describir el resultado"
```

Mantener `.env`, credenciales, logs y `vendor` fuera del índice; `composer.lock` sí se versiona. No se ha elegido una licencia de distribución: Composer declara `proprietary`, aunque el remoto sea público. Acordar la licencia antes de publicar si se desean permisos de reutilización.

Una entrega revisable debe explicar comportamiento, migraciones, validación y límites; actualizar los documentos afectados en el mismo cambio. Para modificar únicamente documentación, comprobar enlaces y consistencia sin volver a sembrar ni destruir la base. No existe aún una política formal de ramas, revisiones ni CI.
