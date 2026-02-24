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
    # Strip all whitespace/newlines from the base64 string before decoding
    CLEAN_B64=$(echo "$DB_SSL_CA_BASE64" | tr -d ' \t\n\r')
    echo "$CLEAN_B64" | base64 -d > /etc/ssl/certs/aiven-ca.pem 2>/dev/null
    if [ $? -eq 0 ] && [ -s /etc/ssl/certs/aiven-ca.pem ]; then
        export MYSQL_ATTR_SSL_CA=/etc/ssl/certs/aiven-ca.pem
        echo "[entrypoint] MYSQL_ATTR_SSL_CA set to /etc/ssl/certs/aiven-ca.pem"
    else
        echo "[entrypoint] WARNING: DB_SSL_CA_BASE64 could not be decoded - skipping SSL cert"
        rm -f /etc/ssl/certs/aiven-ca.pem
    fi
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
