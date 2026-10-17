# Guía del equipo — Mercado Rodriguez

Estado documentado: 17 de octubre de 2026. Esta entrega cubre la persistencia y validación del sistema; la aplicación de gestión todavía requiere sus casos de uso, autenticación e interfaz.

## Ruta de lectura

| Documento | Qué resuelve |
|---|---|
| [Instalación y operación local](instalacion.md) | Preparar otra máquina, ejecutar y diagnosticar el entorno |
| [Arquitectura](arquitectura.md) | Ubicación del código, responsabilidades y relaciones Eloquent |
| [Modelo entidad–relación](entidad-relacion.md) | Diagrama, cardinalidades y reglas adoptadas |
| [Diccionario físico](diccionario-datos.md) | Columnas, tipos, nulabilidad, claves, índices y triggers |
| [Contratos de validación](validaciones.md) | Entradas admitidas y límites de los Form Requests |
| [Datos de demostración](datos-demo.md) | Composición, fechas, tarifas, factories y precauciones de siembra |
| [Pruebas](verificacion.md) | Comandos, cobertura comprobada y escenarios por ampliar |
| [Continuidad del desarrollo](continuidad.md) | Próximas tareas, decisiones pendientes y flujo Git |

## Qué recibe el equipo

Laravel 13 con dependencias fijadas, PostgreSQL, diez modelos Eloquent y sus relaciones, diez Form Requests concretos, diez factories, dos migraciones, dos seeders, una vista de saldos, scripts de preparación/arranque y pruebas de integración. La ruta `/up` comprueba el arranque HTTP; no hay rutas de negocio.

El historial monetario pertenece a las asignaciones originales, aunque cambie el titular del puesto. Las fechas de vigencia usan fin exclusivo. El importe de una obligación es calculado por PostgreSQL; el saldo y la mora se obtienen de obligaciones y pagos.

## Estado del repositorio

El repositorio público [CarlosQE/mercado-rodriguez](https://github.com/CarlosQE/mercado-rodriguez) contiene esta entrega en `main` y está conectado como `origin` mediante SSH. Los miembros del equipo pueden clonar el código y seguir la guía de instalación.

La documentación describe el código existente. Los ejemplos de integración y la secuencia de tareas son propuestas para el trabajo siguiente, no funcionalidades entregadas.
