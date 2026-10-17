# Instalación y operación local

## Requisitos y entorno comprobado

| Componente | Requisito del proyecto | Comprobado el 17/10/2026 |
|---|---|---|
| PHP CLI | 8.3+ y extensiones compatibles con Composer | 8.5.0 |
| Composer | 2.x | 2.8.12 |
| Laravel | versión de `composer.lock` | 13.37.0 |
| PostgreSQL | diseño orientado a PostgreSQL; contenedor 17 | 17 |
| Docker y Compose | necesarios para el entorno de base provisto | Engine 29.8.2 / Compose 5.6.0 |
| Sistema | scripts Bash; pruebas con procesos PHP | WSL2 / Linux |

PHP necesita `pdo_pgsql` y las extensiones exigidas por las dependencias; `composer check-platform-reqs` comprueba el conjunto efectivo. Las factories y la siembra de demo necesitan Faker, incluido en `require-dev`: instalar sin `--no-dev` durante desarrollo. Las pruebas usan `proc_open`, tuberías y conexiones independientes; no se ha comprobado su ejecución en Windows nativo. No hay frontend ni pasos de npm.

Composer tiene `config.platform.php = 8.3.0` para resolver dependencias compatibles con ese mínimo; esto no equivale a una prueba de ejecución en PHP 8.3. Usar `composer install` para respetar el lock; actualizar dependencias debe ser un cambio deliberado.

## Primera preparación

Clonar el repositorio público desde Ubuntu/WSL o Linux:

```bash
git clone https://github.com/CarlosQE/mercado-rodriguez.git
cd mercado-rodriguez
```

Desde la carpeta del proyecto, comprobar las herramientas y preparar el entorno:

```bash
php --version
composer --version
docker info
docker compose version
./scripts/setup --demo
./scripts/serve
```

`setup --demo` hace, en orden: crear `.env` si falta, generar una contraseña local aleatoria en ese archivo nuevo, instalar Composer, generar `APP_KEY` si está vacía, iniciar PostgreSQL, aplicar migraciones y sembrar el demo. Si falla un paso, el script se detiene; revisar el error antes de repetirlo. El catálogo de secciones se inserta fuera de la transacción del demo; si la siembra falla, esas cuatro secciones pueden permanecer.

Abrir `http://127.0.0.1:8000/up`: se espera HTTP 200. La ruta `/` devuelve 404. No existen login, panel ni API de negocio. `/up` no comprueba PostgreSQL; usar además `php artisan migrate:status` y las pruebas.

Para un entorno ya sembrado usar `./scripts/setup` sin `--demo`. Volver a ejecutar la siembra completa sobre puestos/comerciantes/tarifas existentes se rechaza por diseño.

## Variables de entorno

| Variable | Uso |
|---|---|
| `APP_KEY` | Clave privada de Laravel; generar por instalación |
| `APP_ENV`, `APP_DEBUG` | El ejemplo usa entorno local y depuración habilitada |
| `APP_URL` | URL base; ajustar al host y puerto usados si se necesitan URLs generadas |
| `APP_TIMEZONE` | `America/La_Paz` por defecto |
| `DB_CONNECTION` | `pgsql`; el esquema no es portable a SQLite/MySQL |
| `DB_HOST` | `127.0.0.1` para PHP ejecutado en el host |
| `DB_PORT` | `5433` en `.env.example`; PostgreSQL interno usa `5432` |
| `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Base y credenciales locales; Compose también las utiliza |
| `CACHE_STORE`, `SESSION_DRIVER`, `QUEUE_CONNECTION` | Valores de ejemplo; todavía no hay un módulo configurado de sesiones, cache persistente o trabajos |

Si se copia `.env.example` manualmente, `DB_PASSWORD` queda vacío: asignar una contraseña antes de iniciar Compose. El script sólo genera la contraseña al crear un `.env` nuevo; no sobrescribe archivos existentes. No compartir `.env` ni copiar la clave de otra instalación.

`config/database.php` tiene `5432` como valor de reserva si no existe `DB_PORT`; por eso debe conservarse la configuración del `.env` del entorno local.

## PostgreSQL en Docker

El servicio se llama `postgres`, publica sólo `127.0.0.1:5433` y usa el volumen `postgres_data` dentro del proyecto Compose. PHP corre en el host, no en un contenedor. El nombre de usuario provisto por la imagen tiene privilegios administrativos de desarrollo.

```bash
docker compose ps
docker compose logs --tail 80 postgres
docker compose stop postgres
docker compose up -d --wait postgres
```

Cambiar `DB_PORT` en `.env` si el puerto está ocupado y volver a aplicar Compose. `DB_HOST`/`DB_PORT` deben seguir apuntando al puerto publicado en el host. Cambiar la contraseña del archivo después de inicializar el volumen no modifica por sí solo la contraseña del rol de PostgreSQL; coordinar ambas configuraciones sin borrar datos.

Para entrar a la consola sin exponer la contraseña en argumentos:

```bash
docker compose exec postgres sh -c 'psql -U "$POSTGRES_USER" -d "$POSTGRES_DB"'
```

## PostgreSQL ya instalado

Crear una base vacía, preparar un usuario con permisos de migración y configurar `.env`. La primera migración necesita `btree_gist`; si ese usuario no puede instalar extensiones, un administrador debe prepararla en la base. Para ejecutar las pruebas también se requiere crear y eliminar esquemas temporales.

No ejecutar `scripts/setup` en esta modalidad, porque inicia el contenedor local. Desde una copia nueva:

```bash
composer install
cp .env.example .env
# Editar .env con la conexión propia antes de continuar.
chmod 600 .env
php artisan key:generate
php artisan migrate --seed
./scripts/serve
```

No repetir `key:generate` sobre una clave ya utilizada. `--seed` carga datos ficticios; para una base sin demo, ejecutar sólo `php artisan migrate`.

## Arranque, diagnóstico y datos

`./scripts/serve` escucha sólo en localhost. Para cambiar el puerto: `PORT=8001 ./scripts/serve`. Detener con Ctrl+C en la terminal que lo ejecuta. Los logs de Laravel se escriben en `storage/logs/laravel.log`.

| Síntoma | Comprobación |
|---|---|
| `php` o `composer` no existe | Revisar PATH; en la máquina original se instalaron con Herd Lite y accesos en `~/.local/bin` |
| Composer no resuelve Packagist | Probar `curl -I https://repo.packagist.org/packages.json` desde la misma terminal |
| Docker deniega el socket | Probar `docker info` fuera del agente; comprobar permisos del cliente/servicio |
| PostgreSQL no responde | Revisar `docker compose ps`, logs y puerto configurado |
| Falta `pdo_pgsql` | Comprobar `php -m` y `composer check-platform-reqs` |
| Ya existe el demo | Ejecutar setup sin `--demo`; no borrar tablas para resolver el error |
| La prueba no puede crear esquema | Usar una base de desarrollo con permisos CREATE SCHEMA |

`migrate:fresh` y `docker compose down -v` eliminan datos; no forman parte de la puesta en marcha normal. Para experimentar con reinicios completos usar una base o proyecto Compose desechable, distinto del demo que se quiera conservar. Este repositorio no define un procedimiento de despliegue a producción.
