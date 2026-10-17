# Escenarios de aceptación para PostgreSQL

El 17 de octubre de 2026 se ejecutaron `php tests/runtime/check.php` y `tests/sql/demo_assertions.sql` correctamente. La prueba de integración usa un esquema temporal y comprueba migración/rollback, unicidad, intervalos, cálculo de obligaciones, pagos parciales, sobrepagos, anulaciones, historial, estados, inspecciones, hallazgos y validación/autorización de Requests. Dos procesos con conexiones independientes verifican el pago concurrente.

La prueba detectó que CHECK aceptaba un motivo NULL al anular un pago: se corrigió con la migración `2026_10_17_000002_require_payment_annulment_reason` y se incluyó su regresión. Los puntos siguientes son el catálogo de escenarios de aceptación; algunos amplían la cobertura automatizada actual.

1. Migrar, sembrar dos veces: exactamente cuatro secciones iniciales; rollback y migración posterior sin objetos residuales. La extensión compartida permanece.
2. Crear puesto y comerciante; rechazar código y documento compuesto duplicados, área cero y referencia inexistente.
3. Crear asignación abierta; rechazar otra superpuesta. Cerrar el 15 y abrir otra el 15: ambas válidas, historial anterior accesible. Rechazar la modificación del titular antiguo.
4. Crear tarifa; rechazar tarifa superpuesta de la misma sección. Admitir otra sección o intervalos adyacentes. Rechazar cambio del precio de la versión existente.
5. Emitir obligación con sólo IDs, período y fechas; comprobar valores copiados y redondeo. Rechazar duplicado, tarifa de otra sección o fechas fuera de vigencia. Prohibir UPDATE/DELETE.
6. Registrar dos pagos parciales y comprobar saldo. Rechazar sobrepago; anular uno con motivo y recuperar su importe en el saldo. Rechazar borrado y edición del importe.
7. En dos conexiones READ COMMITTED, intentar pagar simultáneamente el saldo completo: sólo una transacción puede confirmar.
8. Obligación vencida y parcialmente pagada aparece en mora; pagada o con vencimiento hoy no aparece.
9. Inspeccionar, transferir e inspeccionar otra vez: última visita del puesto y próxima fecha provienen de la segunda visita; la primera conserva al titular anterior. Rechazar visita fuera de la asignación.
10. Registrar hallazgo con plazo; rechazar subsanación anterior a la visita y subsanación sin evidencia.
11. Clausurar puesto ocupado: estado clausurado. Finalizar clausura: ocupado. Finalizar asignación: libre. No perder registros en ninguno de los cambios.
12. Resolver los Form Requests con usuario autenticado: rechazar campos inválidos y solapamientos con errores de validación; sin usuario, autorización denegada. Confirmar que las protecciones SQL siguen funcionando al escribir directamente.
