-- Sólo lectura; ejecutar después del seeder de demostración.
BEGIN;
DO $$
BEGIN
 IF (SELECT count(*) FROM puestos) <> 300 THEN RAISE EXCEPTION 'Se esperaban 300 puestos'; END IF;
 IF (SELECT count(*) FROM comerciantes) <> 280 THEN RAISE EXCEPTION 'Se esperaban 280 comerciantes'; END IF;
 IF (SELECT count(*) FROM asignaciones) <> 280 THEN RAISE EXCEPTION 'Se esperaban 280 asignaciones'; END IF;
 IF (SELECT count(*) FROM asignaciones WHERE fecha_fin IS NULL) <> 260 THEN RAISE EXCEPTION 'Se esperaban 260 asignaciones actuales'; END IF;
 IF (SELECT count(*) FROM obligaciones) <> 1560 THEN RAISE EXCEPTION 'Se esperaban 1560 obligaciones'; END IF;
 IF (SELECT count(*) FROM inspecciones) <> 520 THEN RAISE EXCEPTION 'Se esperaban 520 inspecciones'; END IF;
 IF (SELECT count(*) FROM clausuras) <> 10 THEN RAISE EXCEPTION 'Se esperaban 10 clausuras'; END IF;
 IF EXISTS(SELECT 1 FROM saldos_obligaciones WHERE saldo < 0) THEN RAISE EXCEPTION 'Hay sobrepagos'; END IF;
 IF NOT EXISTS(SELECT 1 FROM saldos_obligaciones WHERE saldo > 0 AND saldo < importe) THEN RAISE EXCEPTION 'Faltan pagos parciales'; END IF;
 IF NOT EXISTS(SELECT 1 FROM saldos_obligaciones WHERE saldo > 0 AND fecha_vencimiento < CURRENT_DATE) THEN RAISE EXCEPTION 'Faltan obligaciones en mora'; END IF;
 IF EXISTS(SELECT 1 FROM inspecciones i JOIN asignaciones a ON a.id=i.asignacion_id WHERE i.fecha<a.fecha_inicio OR i.fecha>=a.fecha_fin) THEN RAISE EXCEPTION 'Inspección fuera de asignación'; END IF;
 IF EXISTS(SELECT 1 FROM obligaciones o JOIN asignaciones a ON a.id=o.asignacion_id JOIN puestos p ON p.id=a.puesto_id JOIN tarifas t ON t.id=o.tarifa_id WHERE p.seccion_id<>t.seccion_id OR o.periodo<a.fecha_inicio OR o.periodo>=a.fecha_fin OR o.periodo<t.vigente_desde OR o.periodo>=t.vigente_hasta) THEN RAISE EXCEPTION 'Obligación incoherente'; END IF;
END $$;
ROLLBACK;
