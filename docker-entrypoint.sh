#!/bin/sh
set -e

# ---------------------------------------------------------------
# Docker Entrypoint for Laravel on Render + Aiven MySQL
# ---------------------------------------------------------------

# --- Aiven SSL Certificate Setup ---
# If DB_SSL_CA_BASE64 is provided (base64-encoded ca.pem from Aiven console),
# write it to a file and point MYSQL_ATTR_SSL_CA to it.
if [ -n "$DB_SSL_CA_BASE64" ]; then
    echo "[entrypoint] Writing Aiven CA certificate..."
    echo "$DB_SSL_CA_BASE64" | base64 -d > /etc/ssl/certs/aiven-ca.pem
    export MYSQL_ATTR_SSL_CA=/etc/ssl/certs/aiven-ca.pem
    echo "[entrypoint] MYSQL_ATTR_SSL_CA set to /etc/ssl/certs/aiven-ca.pem"
fi

# --- Generate APP_KEY if not already set ---
if [ -z "$APP_KEY" ]; then
    echo "[entrypoint] Generating application key..."
    php artisan key:generate --force
fi

# --- Clear and cache config for production ---
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# --- Run database migrations ---
echo "[entrypoint] Running database migrations..."
php artisan migrate --force

# --- Create storage symlink (public/storage -> storage/app/public) ---
if [ ! -L /var/www/html/public/storage ]; then
    echo "[entrypoint] Creating storage symlink..."
    php artisan storage:link
fi

echo "[entrypoint] Startup complete. Launching Apache..."

# Execute the CMD (apache2-foreground)
exec "$@"
