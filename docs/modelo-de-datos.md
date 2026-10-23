# Modelo de datos

PostgreSQL, 7 tablas. Todas tienen `id` bigint autoincremental, `created_at` y `updated_at`.

```mermaid
erDiagram
    SECCIONES ||--o{ PUESTOS : contiene
    PUESTOS ||--o{ ASIGNACIONES : tiene
    COMERCIANTES ||--o{ ASIGNACIONES : recibe
    ASIGNACIONES ||--o{ OBLIGACIONES : genera
    COMERCIANTES ||--o{ PAGOS : realiza
    PAGOS ||--o{ PAGO_OBLIGACION : aplica
    OBLIGACIONES ||--o{ PAGO_OBLIGACION : cubre
```

## Diccionario de datos

### secciones
| Columna | Tipo | Notas |
|---|---|---|
| nombre | varchar(80) | Único |
| tarifa_m2 | numeric(8,2) | Tarifa mensual por metro cuadrado |

### puestos
| Columna | Tipo | Notas |
|---|---|---|
| seccion_id | bigint | FK a `secciones` |
| codigo | varchar(30) | Único, formato `[A-Z0-9-]+` (por ejemplo `CAR-001`) |
| superficie_m2 | numeric(8,2) | Mayor que 0 |
| estado | varchar(20) | `libre` (por defecto), `ocupado`, `clausurado` |

### comerciantes
| Columna | Tipo | Notas |
|---|---|---|
| documento | varchar(30) | Único |
| nombres, apellidos | varchar(100) | |
| rubro | varchar(80) | |
| telefono | varchar(25) | |
| email | varchar(255) | Opcional |
| direccion | varchar(255) | |
| activo | boolean | Por defecto `true`. Solo los activos reciben asignaciones |

### asignaciones
| Columna | Tipo | Notas |
|---|---|---|
| puesto_id | bigint | FK a `puestos` |
| comerciante_id | bigint | FK a `comerciantes` |
| fecha_inicio | date | |
| fecha_fin | date | Nula = asignación vigente |
| motivo | varchar(255) | |

### obligaciones
| Columna | Tipo | Notas |
|---|---|---|
| asignacion_id | bigint | FK a `asignaciones` |
| periodo | date | Primer día del mes facturado |
| fecha_vencimiento | date | |
| superficie_m2, tarifa_m2 | numeric(8,2) | Copia de los valores al emitir |
| importe | numeric(10,2) | `superficie_m2 × tarifa_m2`, no se recalcula |
| estado | varchar(20) | `pendiente` (por defecto) o `pagada` |

Único: `(asignacion_id, periodo)`.

### pagos
| Columna | Tipo | Notas |
|---|---|---|
| comerciante_id | bigint | FK a `comerciantes` |
| comprobante | varchar(50) | Único, formato `MR-00001` |
| fecha_pago | date | |
| metodo | varchar(20) | `efectivo` o `transferencia` |
| total | numeric(10,2) | Suma de los importes de las obligaciones pagadas |

### pago_obligacion
| Columna | Tipo | Notas |
|---|---|---|
| pago_id | bigint | FK a `pagos` |
| obligacion_id | bigint | FK a `obligaciones` |
| monto | numeric(10,2) | Igual al importe de la obligación (pagos completos) |

Único: `(pago_id, obligacion_id)`.

## Integridad referencial

- Todas las claves foráneas usan `ON UPDATE CASCADE` y `ON DELETE RESTRICT`, salvo `pago_obligacion.pago_id`, que usa `ON DELETE CASCADE`.
- Los valores permitidos de `estado` y `metodo` (columnas de texto), el solapamiento de asignaciones y el cálculo de importes se garantizan en la aplicación (Form Requests, controladores y transacciones).

## Relaciones Eloquent

| Modelo | Relaciones |
|---|---|
| Seccion | `puestos()` |
| Puesto | `seccion()`, `asignaciones()` |
| Comerciante | `asignaciones()`, `pagos()` |
| Asignacion | `puesto()`, `comerciante()`, `obligaciones()` |
| Obligacion | `asignacion()`, `pagos()` |
| Pago | `comerciante()`, `obligaciones()` |
