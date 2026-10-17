# Mercado Rodriguez

Base Laravel para gestionar los alquileres de un mercado municipal ficticio. Esta entrega implementa el diseño y la persistencia: puestos, comerciantes, asignaciones históricas, tarifas por m², obligaciones mensuales, pagos e inspecciones sanitarias.

**Entrada para el equipo: [guía de documentación](docs/README.md).**

## Estado de la entrega

- Laravel 13.37.0 con dependencias fijadas en `composer.lock`; PHP 8.3+ como mínimo de resolución, ejecución comprobada en PHP 8.5.0.
- PostgreSQL 17 mediante Docker Compose, diez tablas de negocio, vista de saldos y dos migraciones.
- Diez modelos Eloquent, diez Form Requests, diez factories y dos seeders.
- Demo con 300 puestos y 280 comerciantes; pruebas de integración con PostgreSQL real.
- Sin autenticación, controladores ni vistas de negocio. Única ruta HTTP propia: salud `/up`.

El repositorio público [CarlosQE/mercado-rodriguez](https://github.com/CarlosQE/mercado-rodriguez) está conectado como `origin` por SSH y sigue vacío. **No se ha hecho push.** Hasta autorizar el primer envío, el equipo necesita recibir la copia local para acceder a los archivos.

## Inicio local

Con PHP, Composer, Docker y Bash disponibles, desde una copia nueva del proyecto:

```bash
cd /ruta/al/mercado-rodriguez
./scripts/setup --demo
./scripts/serve
```

Abrir `http://127.0.0.1:8000/up`; se espera HTTP 200. La raíz `/` devuelve 404 porque no existe interfaz. PostgreSQL queda publicado en `127.0.0.1:5433`.

Para preparar de nuevo un entorno ya sembrado, usar `./scripts/setup` sin `--demo`. El seeder completo rechaza datos existentes; no borra ni actualiza el demo. Los detalles de requisitos, credenciales, instalación manual y diagnóstico están en [instalación](docs/instalacion.md).

## Documentación disponible

| Documento | Contenido |
|---|---|
| [Arquitectura](docs/arquitectura.md) | Estructura, relaciones Eloquent y responsabilidades |
| [Diagrama ER](docs/entidad-relacion.md) | Entidades, cardinalidades y decisiones de negocio |
| [Diccionario de datos](docs/diccionario-datos.md) | Columnas, claves, índices, restricciones y triggers |
| [Validaciones](docs/validaciones.md) | Contratos de los Requests y campos reservados |
| [Demo y factories](docs/datos-demo.md) | Distribución, fechas, tarifas y generación de escenarios |
| [Pruebas](docs/verificacion.md) | Ejecución, resultados y cobertura pendiente |
| [Continuidad](docs/continuidad.md) | Próximas tareas, reglas a confirmar y colaboración Git |

## Verificación

Con PostgreSQL iniciado:

```bash
composer validate --strict
composer check-platform-reqs
composer test
docker compose exec -T postgres sh -c 'psql -U "$POSTGRES_USER" -d "$POSTGRES_DB" -v ON_ERROR_STOP=1' < tests/sql/demo_assertions.sql
```

Las pruebas de integración crean y eliminan un esquema temporal aislado; necesitan permiso CREATE SCHEMA y ejecución de procesos PHP. Las comprobaciones SQL de la siembra sólo leen datos. Ambas suites pasaron el 17/10/2026; se verificaron también `/up` y los pagos simultáneos. Véase la [cobertura detallada](docs/verificacion.md).

`.env`, dependencias y credenciales no se versionan. El usuario de la base de Compose es de desarrollo. No hay procedimiento de producción definido ni licencia de distribución acordada; Composer declara `proprietary`.
