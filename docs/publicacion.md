# Publicación académica: Render + Neon

Se publicará una base NUEVA con los seeders del repositorio, según lo acordado.
No se requiere respaldo ni pgAdmin. No conectar el inicializador a una base real.

## 1. Subir cambios a GitHub

Incluir Dockerfile, .dockerignore, render.yaml, deploy/, config/demo.php,
config/database.php, bootstrap/app.php, routes/console.php, composer.json,
composer.lock, database/seeders/DatabaseSeeder.php y esta guía.
No incluir .env, claves, respaldos ni archivos de credenciales.

## 2. Neon

Crear un proyecto gratuito PostgreSQL 17. En Connect, desactivar Connection
pooling y copiar la conexión DIRECTA. Debe llevar sslmode=require. La conexión
directa permite que el bloqueo de inicialización se mantenga en la misma sesión.
Guardar esta cadena exclusivamente en la variable DATABASE_URL de Render.

## 3. Render

Crear un Blueprint desde el repositorio para utilizar render.yaml, o un Web
Service con runtime Docker, plan Free y las mismas variables del archivo.
No crear una base PostgreSQL gratuita en Render: su plan gratuito caduca a los
30 días. El plan gratuito de Neon tiene cuotas, pero no ese plazo de caducidad.

Completar las variables privadas solicitadas:

| Variable | Valor |
|---|---|
| APP_KEY | Clave Laravel base64 de 32 bytes aleatorios, generada una sola vez |
| APP_URL | URL pública HTTPS del servicio, actualizar si Render asigna otro nombre |
| DATABASE_URL | Conexión directa a la base nueva de Neon con sslmode=require |
| DEMO_ADMIN_PASSWORD | Contraseña privada elegida para admin@mercado.test |
| DEMO_COBRADOR_PASSWORD | Contraseña privada elegida para cobrador@mercado.test |

No usar la contraseña pública del README para estas cuentas desplegadas.
Mantener APP_KEY estable entre despliegues. Las sesiones usan cookies cifradas
para no depender del disco temporal del servidor.

El arranque con INITIALIZE_DEMO=true ejecuta deploy:demo. Aplica las migraciones,
comprueba que las tablas estén vacías y carga el demo dentro de una transacción.
Guarda una marca en deployment_markers para no duplicar datos al reiniciar.
Si hay datos sin esa marca, se detiene sin borrarlos. Si la siembra falla, se
revierte la carga para permitir reintentar. No utiliza migrate:fresh ni db:wipe.
Faker está incluido como dependencia de ejecución para que funcionen las factories.

Después de verificar la primera publicación, se puede cambiar INITIALIZE_DEMO a
false. Cualquier migración futura deberá ejecutarse deliberadamente.

## 4. Comprobaciones

Construir con docker build -t mercado-rodriguez-deploy:local .
Probar portada, login, búsquedas, puestos, asignaciones y pagos. /up comprueba
que Laravel arranca, pero no garantiza que PostgreSQL responda.
Confirmar 280 comerciantes y 320 puestos tras la siembra y que no se duplican
al reiniciar. Probar con registros de demostración, sin información real.

## 5. Fotografías y límites pendientes

Los seeders usan las seis imágenes SVG incluidas en Git; sobreviven a despliegues.
Las fotos NUEVAS subidas por el módulo de administración se escriben en disco
local. Render Free pierde esas cargas al reiniciar o desplegar. Para entregar
ese módulo con persistencia hay que conectar almacenamiento externo y probarlo.

Render Free suspende el servicio tras 15 minutos sin tráfico; el primer acceso
puede tardar. Abrir y comprobar el sistema antes de la exposición.

Referencias: https://render.com/docs/free,
https://render.com/docs/web-services, https://neon.com/pricing.
