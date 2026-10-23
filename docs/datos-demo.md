# Datos de demostración

## Cómo cargarlos

```bash
./scripts/setup --demo     # primera vez, base vacía
php artisan db:seed        # si las migraciones ya están aplicadas y las tablas vacías
```

La siembra **no es idempotente**: crea las secciones con `Seccion::create` y el nombre es único, así que una segunda ejecución falla. Para repetirla hay que usar `php artisan migrate:fresh --seed` (borra todo).

Los nombres, documentos y direcciones salen de Faker y cambian entre ejecuciones. La estructura (cantidades y estados) es determinista, pero las fechas dependen del día en que se siembra.

## Composición

| Elemento | Cantidad |
|---|---:|
| Usuarios | 2: `admin@mercado.test` (admin) y `cobrador@mercado.test` (cobrador), contraseña `mercado123` |
| Ficha del mercado | 1 fila inventada: historia de 4 párrafos, dirección y coordenadas referenciales de La Paz |
| Fotos | 6 composiciones SVG en `public/img/mercado/` |
| Secciones | 4: Carnes (Bs 30), Verduras (Bs 18), Abarrotes (Bs 24), Comida (Bs 35) |
| Puestos | 320 (80 + 100 + 80 + 60), códigos `CAR-001`, `VER-001`, `ABA-001`, `COM-001` |
| Comerciantes | 280 |
| Puestos libres | 20 |
| Puestos clausurados | 20, sin asignación |
| Puestos ocupados | 280 |
| Asignaciones | 300: 280 vigentes y 20 cerradas por transferencia |

De cada 16 puestos, 1 queda libre, 1 clausurado y 14 ocupados.

## Historial

- Las asignaciones vigentes empiezan hace 5 meses (primer día del mes). Los primeros 20 puestos ocupados tuvieron antes un titular cuya asignación se cerró por transferencia hace 2 meses.
- Cada asignación vigente tiene una obligación por mes desde su inicio hasta el mes actual, con vencimiento el día 10. Sembrando en octubre de 2026 son 6 por asignación normal (260) y 3 por las transferidas (20): 1620 obligaciones.
- Según el comerciante: 56 no han pagado nada, 56 pagaron 1 mes y 168 pagaron 3 meses en un solo pago. En total, 224 pagos y 560 obligaciones pagadas.
- Los comprobantes son `MR-00001` en adelante, con huecos donde el comerciante no pagó (el mayor es `MR-00280`). Los pagos nuevos continúan desde el mayor existente.
