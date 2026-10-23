# Mercado Rodríguez

Sistema web para gestionar los alquileres de un mercado municipal ficticio: secciones, puestos, comerciantes, asignaciones, obligaciones mensuales y pagos.

**Entrada para el equipo: [guía de documentación](docs/README.md).**

- **Curso:** Tecnología Web I (UPDS)
- **Repositorio:** https://github.com/CarlosQE/mercado-rodriguez

## Stack

| Capa | Tecnología |
|---|---|
| Backend | Laravel, PHP 8.3+ |
| Base de datos | PostgreSQL (Docker Compose, puerto host 5433) |
| Frontend | Blade + Tailwind por CDN + CSS propio (Inter / Source Serif 4) |
| Acceso | Sesiones de Laravel con roles `admin` y `cobrador` |
| Pruebas | PHPUnit (Feature tests) sobre una base de datos aparte |

## Módulos

| Módulo | Qué hace |
|---|---|
| Secciones | CRUD. Cada sección define la tarifa por m². No se elimina si tiene puestos. |
| Puestos | CRUD con filtros por código, sección y estado (`libre`, `ocupado`, `clausurado`). Un puesto ocupado cambia de estado solo por asignaciones. |
| Comerciantes | CRUD con búsqueda y estado activo/inactivo. No se elimina si tiene asignaciones o pagos. |
| Asignaciones | Historial de quién ocupa cada puesto. Crear ocupa el puesto; cerrar (con fecha de fin) lo libera. Se rechazan intervalos superpuestos. |
| Obligaciones | Se emiten por período. Importe = superficie × tarifa de la sección, copiado al emitir. |
| Pagos | Un pago cubre una o más obligaciones completas de un comerciante. Comprobante correlativo `MR-00001`. |
| El mercado | Portada pública con historia, secciones, ubicación, mapa de OpenStreetMap y galería de fotos. |
| Puestos libres | Listado público paginado, filtrable por sección, con cuota mensual estimada. |
| Mapa | Cuadrícula de puestos por sección para admin y cobrador; cada puesto abre su ficha con historial. |
| Dashboard | Indicadores, ocupación, recaudación, deudores, vencimientos y alertas (solo admin). |

## Reglas de negocio

- `asignaciones.fecha_fin` nula = asignación vigente. Un puesto no puede tener dos asignaciones superpuestas.
- Solo se asignan puestos libres a comerciantes activos.
- **Emisión:** se factura a quien tenga el puesto el primer día del período; no se emiten períodos futuros ni se duplican. Vencimiento por defecto: día 10.
- "Vencida" no se guarda: es una obligación `pendiente` cuyo vencimiento ya pasó.
- **Pagos completos:** sin pagos parciales. Las obligaciones pasan a `pagada` en la misma transacción que crea el pago.
- Asignaciones y pagos no se editan ni se eliminan (historial).

## Acceso y roles

El sistema no tiene registro público. Las cuentas se crean en la siembra de demostración:

| Rol | Correo | Contraseña | Puede |
|---|---|---|---|
| Admin | `admin@mercado.test` | `mercado123` | Todo: CRUD, asignaciones, emisión, pagos, dashboard y administración del mercado. |
| Cobrador | `cobrador@mercado.test` | `mercado123` | Consultar listados, mapa y fichas; registrar pagos. |

> Las credenciales son solo para desarrollo. Cámbialas antes de publicar el sistema.

Los visitantes sin sesión pueden ver la portada (`/`), los puestos libres (`/puestos-libres`) y el formulario de login. El resto de las rutas exige sesión y el rol correspondiente; los botones de escritura también se ocultan en las vistas. Al entrar, el admin va al dashboard y el cobrador al mapa de puestos.

## Puesta en marcha

Requisitos: PHP 8.3+ con `pdo_pgsql`, Composer, Docker.

```bash
./scripts/setup --demo   # crea .env, instala dependencias, levanta Postgres, migra y siembra
./scripts/serve          # http://127.0.0.1:8000
```

Para preparar de nuevo un entorno ya sembrado, usar `./scripts/setup` sin `--demo`. El seeder completo rechaza datos existentes; no borra ni actualiza el demo. Requisitos, credenciales, instalación manual y diagnóstico: [instalación](docs/instalacion.md).

Las fotos que se suben desde **`/admin/fotos`** se guardan en `storage/app/public/mercado` y se muestran con un enlace simbólico. Créalo una vez con:

```bash
php artisan storage:link
```

Detalle de tamaños, formatos y prompts sugeridos para generar fotos con IA: [imágenes con IA](docs/imagenes-ia.md).

Si el servidor integrado va lento, levántalo con varios procesos:

```bash
PHP_CLI_SERVER_WORKERS=4 php artisan serve
```

> `migrate:fresh` y `docker compose down -v` borran los datos.

## Pruebas

Las pruebas usan una base de datos propia (`mercado_rodriguez_test`) y la vacían en cada ejecución. Se niegan a correr si la base activa no termina en `_test`, así que nunca tocan los datos de desarrollo. Crear la base una sola vez:

```bash
php artisan tinker --execute="DB::statement('CREATE DATABASE mercado_rodriguez_test');"
composer test
```

Detalle de lo que cubren en [pruebas](docs/pruebas.md).

## Documentación

| Documento | Contenido |
|---|---|
| [Instalación](docs/instalacion.md) | Requisitos, credenciales, instalación manual y diagnóstico |
| [Modelo de datos](docs/modelo-de-datos.md) | Esquema, diccionario de datos e integridad |
| [Arquitectura](docs/arquitectura.md) | Rutas, validaciones y flujos transaccionales |
| [Datos de demostración](docs/datos-demo.md) | Composición y fechas de la siembra |
| [Pruebas](docs/pruebas.md) | Ejecución y cobertura |
| [Imágenes con IA](docs/imagenes-ia.md) | Tamaño, formato y prompts para la galería |
| [Continuidad](docs/continuidad.md) | Supuestos a confirmar y próximos pasos |
