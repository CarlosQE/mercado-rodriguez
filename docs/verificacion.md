# Pruebas y escenarios de aceptación

## Resultado comprobado

El 17/10/2026 se ejecutaron correctamente las migraciones y la siembra con Laravel 13.37.0, PHP 8.5.0 y PostgreSQL 17. `composer validate --strict`, `composer check-platform-reqs`, las pruebas de integración y el script de comprobación del demo pasaron. `/up` respondió 200 y `/` respondió 404.

Las pruebas detectaron que el CHECK inicial permitía anular un pago con motivo NULL. La migración `2026_10_17_000002_require_payment_annulment_reason` añadió la comprobación explícita y su prueba de regresión está incluida.

Estos resultados corresponden a esa ejecución; no equivalen a certificación de otras versiones de PHP, producción o carga masiva.

## Comandos

Desde la raíz del proyecto, con dependencias y PostgreSQL disponibles:

```bash
composer validate --strict
composer check-platform-reqs
composer test
docker compose exec -T postgres sh -c 'psql -U "$POSTGRES_USER" -d "$POSTGRES_DB" -v ON_ERROR_STOP=1' < tests/sql/demo_assertions.sql
```

`composer test` ejecuta `php tests/runtime/check.php`; devuelve código 0 al pasar y 1 ante un fallo capturado. No necesita PHPUnit ni usa SQLite. Crea un esquema `test_mercado_<identificador>` en la base configurada, cambia el search_path, ejecuta las pruebas y elimina ese esquema en `finally`. La conexión necesita permisos CREATE SCHEMA y de migración. La extensión compartida `btree_gist` permanece instalada. Los procesos trabajadores usan el mismo esquema temporal y conexiones independientes.

La limpieza automática no puede garantizarse si se mata el proceso abruptamente. Antes de eliminar cualquier esquema residual, verificar su nombre y que no haya una prueba activa. No utilizar comandos de borrado sobre `public`.

El script SQL se ejecuta en la base de demostración y termina con ROLLBACK. Si una comprobación falla, `ON_ERROR_STOP=1` hace fallar psql. Sobre una base vacía o con datos diferentes del demo, el rechazo es esperable. Las credenciales se obtienen del entorno del contenedor; no se imprimen contraseñas.

Para comprobar el arranque HTTP, iniciar `./scripts/serve` en otra terminal:

```bash
curl --fail http://127.0.0.1:8000/up
php artisan migrate:status
```

La salud HTTP y la conexión a PostgreSQL son comprobaciones distintas.

## Cobertura automatizada real

| Área | Qué comprueba el código de prueba |
|---|---|
| Migraciones | Aplicación, rollback y nueva aplicación dentro del esquema temporal |
| Puestos | Código duplicado, área cero y estados libre/ocupado/clausurado |
| Vigencias | Rechazo de asignación y tarifa superpuestas |
| Historia | Inmutabilidad de precio, titular y superficie de puesto asignado |
| Obligaciones | Copia de valores, cálculo 6 × 25 = 150, período duplicado y edición rechazada |
| Pagos | Abono parcial, saldo, sobrepago, borrado rechazado y anulación con/sin motivo |
| Transferencias | Cierre/alta adyacentes, conservación de visita anterior y titularidad tras reapertura |
| Sanidad | Visita fuera de asignación y subsanación sin evidencia rechazadas |
| Requests | Datos vacíos rechazados en las diez clases, ejemplo válido en cada una y rechazo sin usuario en la base común |
| Reglas cruzadas de Requests | Asignación superpuesta, sobrepago e inspección fuera de asignación rechazados |
| Concurrencia | Segundo proceso espera el bloqueo; tras confirmar el primero, el pago completo duplicado se rechaza |
| Siembra | Cantidades de puestos/comerciantes/asignaciones/obligaciones/inspecciones/clausuras, saldos no negativos, existencia de mora y pagos parciales, referencias y fechas compatibles |

La prueba concurrente usa READ COMMITTED. Los Requests se resuelven directamente con Laravel y un usuario simulado; no recorren rutas, guardas, políticas ni middleware HTTP. No existe una suite de navegador o interfaz.

La ejecución se acompañó de consultas de inventario que confirmaron 1.300 pagos, 100 hallazgos y la distribución de estados 250/40/10. Estas cantidades adicionales se documentan como resultado observado; no todas son aserciones del script SQL actual.

## Escenarios por ampliar

1. Comprobar unicidad documental compuesta, referencias inexistentes y todas las fronteras de longitud/decimales.
2. Probar intervalos adyacentes, fechas exactas de cierre, actualizaciones con model binding y cierres incompatibles con documentos existentes.
3. Probar ausencia de tarifa, tarifa de otra sección y emisión concurrente/repetida de un mes al implementar el emisor.
4. Comprobar una obligación con vencimiento hoy, completamente pagada y vencida parcialmente pagada, y exactitud del redondeo con cuatro decimales de tarifa.
5. Añadir casos de doble anulación, cambio de importe, pago anterior a emisión y referencia bancaria ausente.
6. Registrar visita tras una transferencia y verificar que reemplaza correctamente a la anterior como última del puesto, incluido desempate por ID.
7. Probar fechas límite/subsanaciones en sus fronteras, seguimiento vencido y puestos sin inspección.
8. Probar la segunda ejecución del seeder: debe rechazar el demo existente sin duplicarlo. Las cuatro secciones usan insertOrIgnore; no afirmar que la siembra completa es idempotente.
9. Añadir pruebas de atomicidad de los casos de uso futuros: el cierre debe revertirse si falla una transferencia o cambio de tarifa.
10. Cubrir autenticación, políticas, traducción de errores y respuestas HTTP cuando existan esas capas.
11. Evaluar consultas masivas, permisos de cuenta de aplicación y auditoría; esta suite no es una prueba de rendimiento ni seguridad integral.

Todo cambio de esquema debe acompañarse de una prueba que falle con el comportamiento anterior y valide el nuevo. Los cambios sólo documentales necesitan revisión de enlaces y consistencia, sin reiniciar ni volver a sembrar la base.
