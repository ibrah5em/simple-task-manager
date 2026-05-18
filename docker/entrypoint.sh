#!/bin/sh
# Boot-time init for the Laravel container.
# Runs as root (supervisord needs to bind :80), drops to www-data for artisan.
set -e

APP_ROOT=/var/www/html
cd "$APP_ROOT"

# Ensure mounted volumes are writable by www-data
# (Docker named volumes default to root:root on first attach)
chown -R www-data:www-data storage bootstrap/cache database
chmod -R ug+rwX storage bootstrap/cache database

# Seed the SQLite file if it's missing (named volume on first boot)
DB_FILE="${DB_DATABASE:-$APP_ROOT/database/database.sqlite}"
if [ ! -f "$DB_FILE" ]; then
    echo "[entrypoint] creating empty SQLite database at $DB_FILE"
    touch "$DB_FILE"
    chown www-data:www-data "$DB_FILE"
fi

# Require APP_KEY — never auto-generate at runtime (sessions would break on restart)
if [ -z "${APP_KEY:-}" ]; then
    echo "[entrypoint] FATAL: APP_KEY is not set. Run:"
    echo "  docker run --rm stm:latest php artisan key:generate --show"
    echo "Add the output to .env, then 'docker compose up -d' again."
    exit 1
fi

# Regenerate package discovery (Dockerfile wiped bootstrap/cache to drop dev pkgs)
echo "[entrypoint] discovering packages"
php artisan package:discover --ansi || true

# Migrations
echo "[entrypoint] running migrations"
php artisan migrate --force --no-interaction

# Production caches (clear first in case the previous boot cached stale config)
echo "[entrypoint] caching config/routes/views"
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Storage symlink (idempotent)
php artisan storage:link 2>/dev/null || true

echo "[entrypoint] starting supervisord"
exec "$@"
