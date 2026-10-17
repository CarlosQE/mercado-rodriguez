# Diccionario de datos PostgreSQL

Documentado el 17/10/2026 desde el esquema aplicado en `public`, incluidas las dos migraciones existentes. Fuente versionada: [SQL inicial](../database/schema/mercado.sql) y [migraciones](../database/migrations). El [diagrama ER](entidad-relacion.md) explica las cardinalidades.

Las tablas de negocio usan PK `bigint` autoincremental y timestamps `created_at`/`updated_at` sin zona horaria, actualmente anulables. Las fechas de vigencia/negocio son `date`. Las FK usan ON DELETE RESTRICT. Los atributos cuyo valor depende de otros registros se protegen además con triggers; no basta con leer los CHECK de una tabla aislada.

`numeric(p,s)` almacena decimales exactos; no reemplazarlo por float. Los campos de fecha final nulos representan vigencia sin fin. Un límite final, cuando existe, es exclusivo.

## `secciones`

| Columna | Tipo SQL | ¿Admite NULL? | Valor inicial / cálculo |
|---|---|---|---|
| `id` | `bigint` | No | `nextval('secciones_id_seq'::regclass)` |
| `nombre` | `character varying(80)` | No | — |
| `intervalo_inspeccion_dias` | `integer` | No | — |
| `created_at` | `timestamp without time zone` | Sí | — |
| `updated_at` | `timestamp without time zone` | Sí | — |

Restricciones:

| Nombre | Definición |
|---|---|
| `secciones_intervalo_inspeccion_dias_check` | `CHECK ((intervalo_inspeccion_dias > 0))` |
| `secciones_nombre_key` | `UNIQUE (nombre)` |
| `secciones_pkey` | `PRIMARY KEY (id)` |

Índices, incluidos los de claves y exclusión:

| Nombre | Definición |
|---|---|
| `secciones_nombre_key` | `CREATE UNIQUE INDEX secciones_nombre_key ON public.secciones USING btree (nombre)` |
| `secciones_pkey` | `CREATE UNIQUE INDEX secciones_pkey ON public.secciones USING btree (id)` |

## `comerciantes`

| Columna | Tipo SQL | ¿Admite NULL? | Valor inicial / cálculo |
|---|---|---|---|
| `id` | `bigint` | No | `nextval('comerciantes_id_seq'::regclass)` |
| `tipo_documento` | `character varying(20)` | No | — |
| `documento` | `character varying(30)` | No | — |
| `nombres` | `character varying(100)` | No | — |
| `apellidos` | `character varying(100)` | No | — |
| `telefono` | `character varying(25)` | No | — |
| `email` | `character varying(254)` | Sí | — |
| `direccion` | `character varying(255)` | No | — |
| `activo` | `boolean` | No | `true` |
| `created_at` | `timestamp without time zone` | Sí | — |
| `updated_at` | `timestamp without time zone` | Sí | — |

Restricciones:

| Nombre | Definición |
|---|---|
| `comerciantes_pkey` | `PRIMARY KEY (id)` |
| `comerciantes_tipo_documento_documento_key` | `UNIQUE (tipo_documento, documento)` |

Índices, incluidos los de claves y exclusión:

| Nombre | Definición |
|---|---|
| `comerciantes_pkey` | `CREATE UNIQUE INDEX comerciantes_pkey ON public.comerciantes USING btree (id)` |
| `comerciantes_tipo_documento_documento_key` | `CREATE UNIQUE INDEX comerciantes_tipo_documento_documento_key ON public.comerciantes USING btree (tipo_documento, documento)` |

## `puestos`

| Columna | Tipo SQL | ¿Admite NULL? | Valor inicial / cálculo |
|---|---|---|---|
| `id` | `bigint` | No | `nextval('puestos_id_seq'::regclass)` |
| `seccion_id` | `bigint` | No | — |
| `codigo` | `character varying(30)` | No | — |
| `superficie_m2` | `numeric(10,2)` | No | — |
| `created_at` | `timestamp without time zone` | Sí | — |
| `updated_at` | `timestamp without time zone` | Sí | — |

Restricciones:

| Nombre | Definición |
|---|---|
| `puestos_codigo_key` | `UNIQUE (codigo)` |
| `puestos_pkey` | `PRIMARY KEY (id)` |
| `puestos_seccion_id_fkey` | `FOREIGN KEY (seccion_id) REFERENCES secciones(id) ON DELETE RESTRICT` |
| `puestos_superficie_m2_check` | `CHECK ((superficie_m2 > (0)::numeric))` |

Índices, incluidos los de claves y exclusión:

| Nombre | Definición |
|---|---|
| `puestos_codigo_key` | `CREATE UNIQUE INDEX puestos_codigo_key ON public.puestos USING btree (codigo)` |
| `puestos_pkey` | `CREATE UNIQUE INDEX puestos_pkey ON public.puestos USING btree (id)` |
| `puestos_seccion_idx` | `CREATE INDEX puestos_seccion_idx ON public.puestos USING btree (seccion_id)` |

## `tarifas`

| Columna | Tipo SQL | ¿Admite NULL? | Valor inicial / cálculo |
|---|---|---|---|
| `id` | `bigint` | No | `nextval('tarifas_id_seq'::regclass)` |
| `seccion_id` | `bigint` | No | — |
| `monto_m2` | `numeric(12,4)` | No | — |
| `vigente_desde` | `date` | No | — |
| `vigente_hasta` | `date` | Sí | — |
| `created_at` | `timestamp without time zone` | Sí | — |
| `updated_at` | `timestamp without time zone` | Sí | — |

Restricciones:

| Nombre | Definición |
|---|---|
| `tarifas_check` | `CHECK (((vigente_hasta IS NULL) OR (vigente_hasta > vigente_desde)))` |
| `tarifas_monto_m2_check` | `CHECK ((monto_m2 > (0)::numeric))` |
| `tarifas_pkey` | `PRIMARY KEY (id)` |
| `tarifas_seccion_id_daterange_excl` | `EXCLUDE USING gist (seccion_id WITH =, daterange(vigente_desde, vigente_hasta, '[)'::text) WITH &&)` |
| `tarifas_seccion_id_fkey` | `FOREIGN KEY (seccion_id) REFERENCES secciones(id) ON DELETE RESTRICT` |

Índices, incluidos los de claves y exclusión:

| Nombre | Definición |
|---|---|
| `tarifas_pkey` | `CREATE UNIQUE INDEX tarifas_pkey ON public.tarifas USING btree (id)` |
| `tarifas_seccion_id_daterange_excl` | `CREATE INDEX tarifas_seccion_id_daterange_excl ON public.tarifas USING gist (seccion_id, daterange(vigente_desde, vigente_hasta, '[)'::text))` |

## `asignaciones`

| Columna | Tipo SQL | ¿Admite NULL? | Valor inicial / cálculo |
|---|---|---|---|
| `id` | `bigint` | No | `nextval('asignaciones_id_seq'::regclass)` |
| `puesto_id` | `bigint` | No | — |
| `comerciante_id` | `bigint` | No | — |
| `fecha_inicio` | `date` | No | — |
| `fecha_fin` | `date` | Sí | — |
| `motivo` | `character varying(255)` | No | — |
| `created_at` | `timestamp without time zone` | Sí | — |
| `updated_at` | `timestamp without time zone` | Sí | — |

Restricciones:

| Nombre | Definición |
|---|---|
| `asignaciones_check` | `CHECK (((fecha_fin IS NULL) OR (fecha_fin > fecha_inicio)))` |
| `asignaciones_comerciante_id_fkey` | `FOREIGN KEY (comerciante_id) REFERENCES comerciantes(id) ON DELETE RESTRICT` |
| `asignaciones_pkey` | `PRIMARY KEY (id)` |
| `asignaciones_puesto_id_daterange_excl` | `EXCLUDE USING gist (puesto_id WITH =, daterange(fecha_inicio, fecha_fin, '[)'::text) WITH &&)` |
| `asignaciones_puesto_id_fkey` | `FOREIGN KEY (puesto_id) REFERENCES puestos(id) ON DELETE RESTRICT` |

Índices, incluidos los de claves y exclusión:

| Nombre | Definición |
|---|---|
| `asignaciones_comerciante_idx` | `CREATE INDEX asignaciones_comerciante_idx ON public.asignaciones USING btree (comerciante_id)` |
| `asignaciones_pkey` | `CREATE UNIQUE INDEX asignaciones_pkey ON public.asignaciones USING btree (id)` |
| `asignaciones_puesto_id_daterange_excl` | `CREATE INDEX asignaciones_puesto_id_daterange_excl ON public.asignaciones USING gist (puesto_id, daterange(fecha_inicio, fecha_fin, '[)'::text))` |

## `clausuras`

| Columna | Tipo SQL | ¿Admite NULL? | Valor inicial / cálculo |
|---|---|---|---|
| `id` | `bigint` | No | `nextval('clausuras_id_seq'::regclass)` |
| `puesto_id` | `bigint` | No | — |
| `fecha_inicio` | `date` | No | — |
| `fecha_fin` | `date` | Sí | — |
| `motivo` | `text` | No | — |
| `created_at` | `timestamp without time zone` | Sí | — |
| `updated_at` | `timestamp without time zone` | Sí | — |

Restricciones:

| Nombre | Definición |
|---|---|
| `clausuras_check` | `CHECK (((fecha_fin IS NULL) OR (fecha_fin > fecha_inicio)))` |
| `clausuras_pkey` | `PRIMARY KEY (id)` |
| `clausuras_puesto_id_daterange_excl` | `EXCLUDE USING gist (puesto_id WITH =, daterange(fecha_inicio, fecha_fin, '[)'::text) WITH &&)` |
| `clausuras_puesto_id_fkey` | `FOREIGN KEY (puesto_id) REFERENCES puestos(id) ON DELETE RESTRICT` |

Índices, incluidos los de claves y exclusión:

| Nombre | Definición |
|---|---|
| `clausuras_pkey` | `CREATE UNIQUE INDEX clausuras_pkey ON public.clausuras USING btree (id)` |
| `clausuras_puesto_id_daterange_excl` | `CREATE INDEX clausuras_puesto_id_daterange_excl ON public.clausuras USING gist (puesto_id, daterange(fecha_inicio, fecha_fin, '[)'::text))` |

## `obligaciones`

| Columna | Tipo SQL | ¿Admite NULL? | Valor inicial / cálculo |
|---|---|---|---|
| `id` | `bigint` | No | `nextval('obligaciones_id_seq'::regclass)` |
| `asignacion_id` | `bigint` | No | — |
| `tarifa_id` | `bigint` | No | — |
| `periodo` | `date` | No | — |
| `fecha_emision` | `date` | No | — |
| `fecha_vencimiento` | `date` | No | — |
| `superficie_m2` | `numeric(10,2)` | No | — |
| `monto_m2` | `numeric(12,4)` | No | — |
| `importe` | `numeric(18,2)` | Sí | `round((superficie_m2 * monto_m2), 2)` (generado) |
| `created_at` | `timestamp without time zone` | Sí | — |
| `updated_at` | `timestamp without time zone` | Sí | — |

Restricciones:

| Nombre | Definición |
|---|---|
| `obligaciones_asignacion_id_fkey` | `FOREIGN KEY (asignacion_id) REFERENCES asignaciones(id) ON DELETE RESTRICT` |
| `obligaciones_asignacion_id_periodo_key` | `UNIQUE (asignacion_id, periodo)` |
| `obligaciones_check` | `CHECK ((fecha_vencimiento >= fecha_emision))` |
| `obligaciones_monto_m2_check` | `CHECK ((monto_m2 > (0)::numeric))` |
| `obligaciones_periodo_check` | `CHECK ((EXTRACT(day FROM periodo) = (1)::numeric))` |
| `obligaciones_pkey` | `PRIMARY KEY (id)` |
| `obligaciones_superficie_m2_check` | `CHECK ((superficie_m2 > (0)::numeric))` |
| `obligaciones_tarifa_id_fkey` | `FOREIGN KEY (tarifa_id) REFERENCES tarifas(id) ON DELETE RESTRICT` |

Índices, incluidos los de claves y exclusión:

| Nombre | Definición |
|---|---|
| `obligaciones_asignacion_id_periodo_key` | `CREATE UNIQUE INDEX obligaciones_asignacion_id_periodo_key ON public.obligaciones USING btree (asignacion_id, periodo)` |
| `obligaciones_pkey` | `CREATE UNIQUE INDEX obligaciones_pkey ON public.obligaciones USING btree (id)` |
| `obligaciones_tarifa_idx` | `CREATE INDEX obligaciones_tarifa_idx ON public.obligaciones USING btree (tarifa_id)` |
| `obligaciones_vencimiento_idx` | `CREATE INDEX obligaciones_vencimiento_idx ON public.obligaciones USING btree (fecha_vencimiento)` |

## `pagos`

| Columna | Tipo SQL | ¿Admite NULL? | Valor inicial / cálculo |
|---|---|---|---|
| `id` | `bigint` | No | `nextval('pagos_id_seq'::regclass)` |
| `obligacion_id` | `bigint` | No | — |
| `recibo` | `character varying(50)` | No | — |
| `fecha_pago` | `date` | No | — |
| `importe` | `numeric(18,2)` | No | — |
| `metodo` | `character varying(20)` | No | — |
| `referencia` | `character varying(100)` | Sí | — |
| `anulado_en` | `timestamp without time zone` | Sí | — |
| `motivo_anulacion` | `text` | Sí | — |
| `created_at` | `timestamp without time zone` | Sí | — |
| `updated_at` | `timestamp without time zone` | Sí | — |

Restricciones:

| Nombre | Definición |
|---|---|
| `pagos_anulacion_motivo_check` | `CHECK ((((anulado_en IS NULL) AND (motivo_anulacion IS NULL)) OR ((anulado_en IS NOT NULL) AND (motivo_anulacion IS NOT NULL) AND (length(TRIM(BOTH FROM motivo_anulacion)) > 0))))` |
| `pagos_check` | `CHECK ((((anulado_en IS NULL) AND (motivo_anulacion IS NULL)) OR ((anulado_en IS NOT NULL) AND (length(TRIM(BOTH FROM motivo_anulacion)) > 0))))` |
| `pagos_check1` | `CHECK ((((metodo)::text <> 'transferencia'::text) OR ((length(TRIM(BOTH FROM referencia)) > 0) AND (referencia IS NOT NULL))))` |
| `pagos_importe_check` | `CHECK ((importe > (0)::numeric))` |
| `pagos_metodo_check` | `CHECK (((metodo)::text = ANY ((ARRAY['efectivo'::character varying, 'transferencia'::character varying])::text[])))` |
| `pagos_obligacion_id_fkey` | `FOREIGN KEY (obligacion_id) REFERENCES obligaciones(id) ON DELETE RESTRICT` |
| `pagos_pkey` | `PRIMARY KEY (id)` |
| `pagos_recibo_key` | `UNIQUE (recibo)` |

Índices, incluidos los de claves y exclusión:

| Nombre | Definición |
|---|---|
| `pagos_obligacion_idx` | `CREATE INDEX pagos_obligacion_idx ON public.pagos USING btree (obligacion_id)` |
| `pagos_pkey` | `CREATE UNIQUE INDEX pagos_pkey ON public.pagos USING btree (id)` |
| `pagos_recibo_key` | `CREATE UNIQUE INDEX pagos_recibo_key ON public.pagos USING btree (recibo)` |

## `inspecciones`

| Columna | Tipo SQL | ¿Admite NULL? | Valor inicial / cálculo |
|---|---|---|---|
| `id` | `bigint` | No | `nextval('inspecciones_id_seq'::regclass)` |
| `asignacion_id` | `bigint` | No | — |
| `fecha` | `date` | No | — |
| `inspector` | `character varying(150)` | No | — |
| `resultado` | `character varying(30)` | No | — |
| `proxima_fecha` | `date` | No | — |
| `observaciones` | `text` | Sí | — |
| `created_at` | `timestamp without time zone` | Sí | — |
| `updated_at` | `timestamp without time zone` | Sí | — |

Restricciones:

| Nombre | Definición |
|---|---|
| `inspecciones_asignacion_id_fkey` | `FOREIGN KEY (asignacion_id) REFERENCES asignaciones(id) ON DELETE RESTRICT` |
| `inspecciones_check` | `CHECK ((proxima_fecha > fecha))` |
| `inspecciones_pkey` | `PRIMARY KEY (id)` |
| `inspecciones_resultado_check` | `CHECK (((resultado)::text = ANY ((ARRAY['aprobada'::character varying, 'con_observaciones'::character varying, 'no_aprobada'::character varying])::text[])))` |

Índices, incluidos los de claves y exclusión:

| Nombre | Definición |
|---|---|
| `inspecciones_asignacion_fecha_idx` | `CREATE INDEX inspecciones_asignacion_fecha_idx ON public.inspecciones USING btree (asignacion_id, fecha DESC, id DESC)` |
| `inspecciones_pkey` | `CREATE UNIQUE INDEX inspecciones_pkey ON public.inspecciones USING btree (id)` |

## `hallazgos`

| Columna | Tipo SQL | ¿Admite NULL? | Valor inicial / cálculo |
|---|---|---|---|
| `id` | `bigint` | No | `nextval('hallazgos_id_seq'::regclass)` |
| `inspeccion_id` | `bigint` | No | — |
| `descripcion` | `text` | No | — |
| `fecha_limite` | `date` | No | — |
| `subsanado_en` | `date` | Sí | — |
| `evidencia` | `text` | Sí | — |
| `created_at` | `timestamp without time zone` | Sí | — |
| `updated_at` | `timestamp without time zone` | Sí | — |

Restricciones:

| Nombre | Definición |
|---|---|
| `hallazgos_check` | `CHECK (((subsanado_en IS NULL) OR ((evidencia IS NOT NULL) AND (length(TRIM(BOTH FROM evidencia)) > 0))))` |
| `hallazgos_inspeccion_id_fkey` | `FOREIGN KEY (inspeccion_id) REFERENCES inspecciones(id) ON DELETE RESTRICT` |
| `hallazgos_pkey` | `PRIMARY KEY (id)` |

Índices, incluidos los de claves y exclusión:

| Nombre | Definición |
|---|---|
| `hallazgos_inspeccion_idx` | `CREATE INDEX hallazgos_inspeccion_idx ON public.hallazgos USING btree (inspeccion_id)` |
| `hallazgos_pkey` | `CREATE UNIQUE INDEX hallazgos_pkey ON public.hallazgos USING btree (id)` |

## Vista `saldos_obligaciones`

Una fila por obligación: `obligacion_id`, `asignacion_id`, `periodo`, `fecha_vencimiento`, `importe`, `saldo`. Resta del importe la suma de pagos cuyo `anulado_en` es nulo; sin pagos, resta cero. No es una tabla de actualización ni tiene un modelo Eloquent propio.

```sql
SELECT * FROM saldos_obligaciones
WHERE fecha_vencimiento < CURRENT_DATE AND saldo > 0;
```

La mora no incluye el propio día del vencimiento. El estado `pagado` tampoco se almacena: se deduce de saldo cero. No hay intereses, multas o cuotas de prorrateo en este cálculo.

## Funciones y triggers

| Función | Tablas / operaciones | Garantía |
|---|---|---|
| `preparar_obligacion()` | obligaciones: INSERT, UPDATE, DELETE | Sólo permite alta; exige titular/tarifa vigentes al inicio del mes y sección compatible; copia superficie/precio |
| `validar_pago()` | pagos: INSERT, UPDATE, DELETE | Bloquea obligación, valida fecha, rechaza sobrepago y borrado; sólo admite pasar de vigente a anulado manteniendo datos del recibo |
| `proteger_historial()` | asignaciones, tarifas, puestos: UPDATE, DELETE | Impide borrado; mantiene identidad de asignación y versión tarifaria; impide cierres incompatibles y modificación de sección/superficie tras asignar |
| `validar_inspeccion()` | inspecciones: INSERT, UPDATE, DELETE | Sólo permite alta, con fecha no futura y dentro de la asignación |
| `validar_hallazgo()` | hallazgos: INSERT, UPDATE, DELETE | Impide borrado; sólo permite cambiar subsanación/evidencia; verifica fechas respecto de visita y día actual |

La segunda migración añade `pagos_anulacion_motivo_check`: además de los triggers, toda anulación necesita motivo explícitamente no nulo y con texto. El esquema inicial conserva su CHECK anterior; el adicional corrige su aceptación de NULL.

No hay trigger de borrado/inmutabilidad de clausuras. Los campos de identidad del comerciante no tienen auditoría de versiones. El [documento de arquitectura](arquitectura.md) detalla estos límites; la matriz de Requests añade validaciones de formato que no necesariamente existen en SQL.

## Notas de mantenimiento

No editar este inventario para declarar una restricción que todavía no se ha migrado. Cuando cambie el esquema, añadir la migración, verificarla, actualizar este documento y las pruebas. Los nombres automáticos de restricciones reflejan las migraciones actuales. La tabla técnica `migrations` de Laravel no forma parte de las diez entidades de negocio.
