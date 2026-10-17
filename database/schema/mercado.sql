CREATE EXTENSION IF NOT EXISTS btree_gist;
CREATE TABLE secciones (
 id bigserial PRIMARY KEY, nombre varchar(80) NOT NULL UNIQUE,
 intervalo_inspeccion_dias integer NOT NULL CHECK (intervalo_inspeccion_dias > 0),
 created_at timestamp, updated_at timestamp
);
CREATE TABLE comerciantes (
 id bigserial PRIMARY KEY, tipo_documento varchar(20) NOT NULL,
 documento varchar(30) NOT NULL, nombres varchar(100) NOT NULL,
 apellidos varchar(100) NOT NULL, telefono varchar(25) NOT NULL,
 email varchar(254), direccion varchar(255) NOT NULL,
 activo boolean NOT NULL DEFAULT true, created_at timestamp, updated_at timestamp,
 UNIQUE(tipo_documento, documento)
);
CREATE TABLE puestos (
 id bigserial PRIMARY KEY, seccion_id bigint NOT NULL REFERENCES secciones ON DELETE RESTRICT,
 codigo varchar(30) NOT NULL UNIQUE, superficie_m2 numeric(10,2) NOT NULL CHECK(superficie_m2 > 0),
 created_at timestamp, updated_at timestamp
);
CREATE INDEX puestos_seccion_idx ON puestos(seccion_id);
CREATE TABLE tarifas (
 id bigserial PRIMARY KEY, seccion_id bigint NOT NULL REFERENCES secciones ON DELETE RESTRICT,
 monto_m2 numeric(12,4) NOT NULL CHECK(monto_m2 > 0),
 vigente_desde date NOT NULL, vigente_hasta date,
 created_at timestamp, updated_at timestamp,
 CHECK(vigente_hasta IS NULL OR vigente_hasta > vigente_desde),
 EXCLUDE USING gist (seccion_id WITH =, daterange(vigente_desde, vigente_hasta, '[)') WITH &&)
);
CREATE TABLE asignaciones (
 id bigserial PRIMARY KEY, puesto_id bigint NOT NULL REFERENCES puestos ON DELETE RESTRICT,
 comerciante_id bigint NOT NULL REFERENCES comerciantes ON DELETE RESTRICT,
 fecha_inicio date NOT NULL, fecha_fin date, motivo varchar(255) NOT NULL,
 created_at timestamp, updated_at timestamp,
 CHECK(fecha_fin IS NULL OR fecha_fin > fecha_inicio),
 EXCLUDE USING gist (puesto_id WITH =, daterange(fecha_inicio, fecha_fin, '[)') WITH &&)
);
CREATE INDEX asignaciones_comerciante_idx ON asignaciones(comerciante_id);
CREATE TABLE clausuras (
 id bigserial PRIMARY KEY, puesto_id bigint NOT NULL REFERENCES puestos ON DELETE RESTRICT,
 fecha_inicio date NOT NULL, fecha_fin date, motivo text NOT NULL,
 created_at timestamp, updated_at timestamp,
 CHECK(fecha_fin IS NULL OR fecha_fin > fecha_inicio),
 EXCLUDE USING gist (puesto_id WITH =, daterange(fecha_inicio, fecha_fin, '[)') WITH &&)
);
CREATE TABLE obligaciones (
 id bigserial PRIMARY KEY, asignacion_id bigint NOT NULL REFERENCES asignaciones ON DELETE RESTRICT,
 tarifa_id bigint NOT NULL REFERENCES tarifas ON DELETE RESTRICT,
 periodo date NOT NULL CHECK(EXTRACT(DAY FROM periodo) = 1),
 fecha_emision date NOT NULL, fecha_vencimiento date NOT NULL,
 superficie_m2 numeric(10,2) NOT NULL CHECK(superficie_m2 > 0),
 monto_m2 numeric(12,4) NOT NULL CHECK(monto_m2 > 0),
 importe numeric(18,2) GENERATED ALWAYS AS (round(superficie_m2 * monto_m2, 2)) STORED,
 created_at timestamp, updated_at timestamp,
 CHECK(fecha_vencimiento >= fecha_emision), UNIQUE(asignacion_id, periodo)
);
CREATE INDEX obligaciones_vencimiento_idx ON obligaciones(fecha_vencimiento);
CREATE INDEX obligaciones_tarifa_idx ON obligaciones(tarifa_id);
CREATE TABLE pagos (
 id bigserial PRIMARY KEY, obligacion_id bigint NOT NULL REFERENCES obligaciones ON DELETE RESTRICT,
 recibo varchar(50) NOT NULL UNIQUE, fecha_pago date NOT NULL,
 importe numeric(18,2) NOT NULL CHECK(importe > 0),
 metodo varchar(20) NOT NULL CHECK(metodo IN ('efectivo', 'transferencia')),
 referencia varchar(100), anulado_en timestamp, motivo_anulacion text,
 created_at timestamp, updated_at timestamp,
 CHECK((anulado_en IS NULL AND motivo_anulacion IS NULL) OR
       (anulado_en IS NOT NULL AND length(trim(motivo_anulacion)) > 0)),
 CHECK(metodo <> 'transferencia' OR length(trim(referencia)) > 0 AND referencia IS NOT NULL)
);
CREATE INDEX pagos_obligacion_idx ON pagos(obligacion_id);
CREATE TABLE inspecciones (
 id bigserial PRIMARY KEY, asignacion_id bigint NOT NULL REFERENCES asignaciones ON DELETE RESTRICT,
 fecha date NOT NULL, inspector varchar(150) NOT NULL,
 resultado varchar(30) NOT NULL CHECK(resultado IN ('aprobada', 'con_observaciones', 'no_aprobada')),
 proxima_fecha date NOT NULL CHECK(proxima_fecha > fecha), observaciones text,
 created_at timestamp, updated_at timestamp
);
CREATE INDEX inspecciones_asignacion_fecha_idx ON inspecciones(asignacion_id, fecha DESC, id DESC);
CREATE TABLE hallazgos (
 id bigserial PRIMARY KEY, inspeccion_id bigint NOT NULL REFERENCES inspecciones ON DELETE RESTRICT,
 descripcion text NOT NULL, fecha_limite date NOT NULL, subsanado_en date, evidencia text,
 created_at timestamp, updated_at timestamp,
 CHECK(subsanado_en IS NULL OR (evidencia IS NOT NULL AND length(trim(evidencia)) > 0))
);
CREATE INDEX hallazgos_inspeccion_idx ON hallazgos(inspeccion_id);
CREATE VIEW saldos_obligaciones AS
 SELECT o.id AS obligacion_id, o.asignacion_id, o.periodo, o.fecha_vencimiento, o.importe,
 o.importe - COALESCE(sum(p.importe) FILTER (WHERE p.anulado_en IS NULL), 0) AS saldo
 FROM obligaciones o LEFT JOIN pagos p ON p.obligacion_id = o.id GROUP BY o.id;

-- Importes y referencias históricas se validan también fuera de HTTP.
CREATE FUNCTION preparar_obligacion() RETURNS trigger LANGUAGE plpgsql AS $$
DECLARE a asignaciones; p puestos; t tarifas;
BEGIN
 IF TG_OP <> 'INSERT' THEN RAISE EXCEPTION 'Las obligaciones emitidas son inmutables'; END IF;
 SELECT * INTO STRICT a FROM asignaciones WHERE id = NEW.asignacion_id FOR SHARE;
 SELECT * INTO STRICT p FROM puestos WHERE id = a.puesto_id FOR SHARE;
 SELECT * INTO STRICT t FROM tarifas WHERE id = NEW.tarifa_id FOR SHARE;
 IF NEW.periodo < a.fecha_inicio OR (a.fecha_fin IS NOT NULL AND NEW.periodo >= a.fecha_fin)
 OR t.seccion_id <> p.seccion_id OR NEW.periodo < t.vigente_desde
 OR (t.vigente_hasta IS NOT NULL AND NEW.periodo >= t.vigente_hasta) THEN
 RAISE EXCEPTION 'Asignación o tarifa no vigente en el período'; END IF;
 NEW.superficie_m2 := p.superficie_m2;
 NEW.monto_m2 := t.monto_m2;
 RETURN NEW;
END $$;
CREATE TRIGGER obligaciones_integridad BEFORE INSERT OR UPDATE OR DELETE ON obligaciones
 FOR EACH ROW EXECUTE FUNCTION preparar_obligacion();

CREATE FUNCTION validar_pago() RETURNS trigger LANGUAGE plpgsql AS $$
DECLARE total numeric; pagado numeric; emision date;
BEGIN
 IF TG_OP = 'DELETE' THEN RAISE EXCEPTION 'Anule el recibo; no lo elimine'; END IF;
 IF TG_OP = 'UPDATE' THEN
  IF (NEW.obligacion_id, NEW.recibo, NEW.fecha_pago, NEW.importe, NEW.metodo, NEW.referencia)
   IS DISTINCT FROM (OLD.obligacion_id, OLD.recibo, OLD.fecha_pago, OLD.importe, OLD.metodo, OLD.referencia)
   OR OLD.anulado_en IS NOT NULL OR NEW.anulado_en IS NULL THEN
   RAISE EXCEPTION 'Sólo se permite anular un pago vigente'; END IF;
 END IF;
 SELECT importe, fecha_emision INTO STRICT total, emision FROM obligaciones WHERE id = NEW.obligacion_id FOR UPDATE;
 IF NEW.fecha_pago < emision OR NEW.fecha_pago > CURRENT_DATE THEN RAISE EXCEPTION 'Fecha de pago inválida'; END IF;
 SELECT COALESCE(sum(importe), 0) INTO pagado FROM pagos
 WHERE obligacion_id = NEW.obligacion_id AND anulado_en IS NULL AND id <> NEW.id;
 IF NEW.anulado_en IS NULL AND pagado + NEW.importe > total THEN RAISE EXCEPTION 'El pago excede el saldo'; END IF;
 RETURN NEW;
END $$;
CREATE TRIGGER pagos_integridad BEFORE INSERT OR UPDATE OR DELETE ON pagos
 FOR EACH ROW EXECUTE FUNCTION validar_pago();

CREATE FUNCTION proteger_historial() RETURNS trigger LANGUAGE plpgsql AS $$
BEGIN
 IF TG_OP = 'DELETE' THEN RAISE EXCEPTION 'El historial no se elimina'; END IF;
 IF TG_TABLE_NAME = 'asignaciones' THEN
  IF (NEW.puesto_id, NEW.comerciante_id, NEW.fecha_inicio) IS DISTINCT FROM
     (OLD.puesto_id, OLD.comerciante_id, OLD.fecha_inicio) THEN RAISE EXCEPTION 'La titularidad histórica no se modifica'; END IF;
  IF EXISTS(SELECT 1 FROM obligaciones WHERE asignacion_id = OLD.id AND NEW.fecha_fin IS NOT NULL AND periodo >= NEW.fecha_fin)
  OR EXISTS(SELECT 1 FROM inspecciones WHERE asignacion_id = OLD.id AND NEW.fecha_fin IS NOT NULL AND fecha >= NEW.fecha_fin)
  THEN RAISE EXCEPTION 'El cierre dejaría registros fuera de su asignación'; END IF;
 ELSIF TG_TABLE_NAME = 'tarifas' THEN
  IF (NEW.seccion_id, NEW.monto_m2, NEW.vigente_desde) IS DISTINCT FROM
     (OLD.seccion_id, OLD.monto_m2, OLD.vigente_desde) THEN RAISE EXCEPTION 'Cree una nueva versión de tarifa'; END IF;
  IF EXISTS(SELECT 1 FROM obligaciones WHERE tarifa_id = OLD.id AND NEW.vigente_hasta IS NOT NULL AND periodo >= NEW.vigente_hasta)
  THEN RAISE EXCEPTION 'La tarifa está usada fuera del nuevo intervalo'; END IF;
 ELSIF TG_TABLE_NAME = 'puestos' THEN
  IF (NEW.seccion_id, NEW.superficie_m2) IS DISTINCT FROM (OLD.seccion_id, OLD.superficie_m2)
  AND EXISTS(SELECT 1 FROM asignaciones WHERE puesto_id = OLD.id) THEN RAISE EXCEPTION 'Superficie y sección quedan fijas tras asignar el puesto'; END IF;
 END IF;
 RETURN NEW;
END $$;
CREATE TRIGGER asignaciones_historial BEFORE UPDATE OR DELETE ON asignaciones FOR EACH ROW EXECUTE FUNCTION proteger_historial();
CREATE TRIGGER tarifas_historial BEFORE UPDATE OR DELETE ON tarifas FOR EACH ROW EXECUTE FUNCTION proteger_historial();
CREATE TRIGGER puestos_historial BEFORE UPDATE OR DELETE ON puestos FOR EACH ROW EXECUTE FUNCTION proteger_historial();

CREATE FUNCTION validar_inspeccion() RETURNS trigger LANGUAGE plpgsql AS $$
DECLARE a asignaciones;
BEGIN
 IF TG_OP <> 'INSERT' THEN RAISE EXCEPTION 'Las inspecciones son históricas; registre una nueva'; END IF;
 SELECT * INTO STRICT a FROM asignaciones WHERE id = NEW.asignacion_id FOR SHARE;
 IF NEW.fecha < a.fecha_inicio OR (a.fecha_fin IS NOT NULL AND NEW.fecha >= a.fecha_fin) OR NEW.fecha > CURRENT_DATE THEN
 RAISE EXCEPTION 'Fecha de inspección fuera de la asignación'; END IF;
 RETURN NEW;
END $$;
CREATE TRIGGER inspecciones_integridad BEFORE INSERT OR UPDATE OR DELETE ON inspecciones FOR EACH ROW EXECUTE FUNCTION validar_inspeccion();
CREATE FUNCTION validar_hallazgo() RETURNS trigger LANGUAGE plpgsql AS $$
DECLARE realizada date;
BEGIN
 IF TG_OP = 'DELETE' THEN RAISE EXCEPTION 'Los hallazgos no se eliminan'; END IF;
 IF TG_OP = 'UPDATE' AND (NEW.inspeccion_id, NEW.descripcion, NEW.fecha_limite) IS DISTINCT FROM
 (OLD.inspeccion_id, OLD.descripcion, OLD.fecha_limite) THEN RAISE EXCEPTION 'Sólo se permite registrar la subsanación'; END IF;
 SELECT fecha INTO STRICT realizada FROM inspecciones WHERE id = NEW.inspeccion_id;
 IF NEW.fecha_limite < realizada OR NEW.subsanado_en < realizada OR NEW.subsanado_en > CURRENT_DATE THEN
 RAISE EXCEPTION 'Fechas de hallazgo inválidas'; END IF;
 RETURN NEW;
END $$;
CREATE TRIGGER hallazgos_integridad BEFORE INSERT OR UPDATE OR DELETE ON hallazgos FOR EACH ROW EXECUTE FUNCTION validar_hallazgo();
