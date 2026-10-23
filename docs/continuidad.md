# Continuidad y supuestos

## Supuestos a confirmar con el responsable funcional

Estos supuestos están reflejados en el sistema; no son reglas municipales confirmadas.

| Tema | Supuesto actual |
|---|---|
| Moneda | Bolivianos; no hay soporte multimoneda |
| Altas a mitad de mes | Se factura a quien tiene el puesto el día 1, sin prorrateo |
| Vencimiento | Día 10 por defecto; la emisión es manual, no automática |
| Tarifa | Una por sección, sin historial de vigencias; cada obligación guarda la tarifa vigente al emitir |
| Clausura | Estado manual del puesto; un puesto clausurado no se puede asignar |
| Transferencia | Se cierra la asignación y se abre otra; las deudas quedan con el titular anterior |
| Pagos | Siempre completos; no hay anulación ni notas de crédito |
| Comerciantes | Pueden tener varias asignaciones simultáneas; no hay límite |
| Usuarios | No hay autenticación, roles ni auditoría |

## Posibles mejoras

- Acción "Transferir puesto" en un solo paso.
- Autenticación y roles.
- Anulación de pagos con motivo.
- Restricción de exclusión (`EXCLUDE USING gist`) en PostgreSQL para impedir asignaciones superpuestas a nivel de base de datos.
- Panel inicial con puestos por estado y total adeudado.
