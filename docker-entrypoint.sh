#!/bin/sh
set -e

# ---------------------------------------------------------------
# Docker Entrypoint for Laravel on Render + Aiven MySQL
# ---------------------------------------------------------------

APP_DIR=/var/www/html

# --- Debug: print received DB env vars (password masked) ---
echo "[entrypoint] DB_HOST=${DB_HOST}"
echo "[entrypoint] DB_PORT=${DB_PORT}"
echo "[entrypoint] DB_DATABASE=${DB_DATABASE}"
echo "[entrypoint] DB_USERNAME=${DB_USERNAME}"
echo "[entrypoint] DB_PASSWORD=$(echo "$DB_PASSWORD" | cut -c1-4)****"
echo "[entrypoint] APP_KEY=$(echo "$APP_KEY" | cut -c1-10)..."

# --- Aiven SSL Certificate Setup ---
SSL_CA_PATH=/etc/ssl/certs/aiven-ca.pem
if [ -n "$DB_SSL_CA_BASE64" ]; then
    echo "[entrypoint] Writing Aiven CA certificate..."
    CLEAN_B64=$(echo "$DB_SSL_CA_BASE64" | tr -d ' \t\n\r')
    echo "$CLEAN_B64" | base64 -d > "$SSL_CA_PATH" 2>/dev/null
    if [ $? -eq 0 ] && [ -s "$SSL_CA_PATH" ]; then
        echo "[entrypoint] Aiven CA cert written OK"
    else
        echo "[entrypoint] WARNING: Could not decode DB_SSL_CA_BASE64 - skipping SSL cert"
        SSL_CA_PATH=""
        rm -f /etc/ssl/certs/aiven-ca.pem
    fi
else
    SSL_CA_PATH=""
fi

# --- Write .env file from Render environment variables ---
# Apache does not forward shell env vars to PHP, so we write them
# explicitly into .env so Laravel's dotenv loader always finds them.
echo "[entrypoint] Writing .env from environment variables..."
cat > "$APP_DIR/.env" <<EOF
APP_NAME="${APP_NAME:-LMS}"
APP_ENV="${APP_ENV:-production}"
APP_KEY="${APP_KEY}"
APP_DEBUG="${APP_DEBUG:-false}"
APP_URL="${APP_URL:-http://localhost}"

LOG_CHANNEL=${LOG_CHANNEL:-stderr}
LOG_DEPRECATIONS_CHANNEL=${LOG_DEPRECATIONS_CHANNEL:-null}
LOG_LEVEL=${LOG_LEVEL:-error}

DB_CONNECTION=${DB_CONNECTION:-mysql}
DB_HOST=${DB_HOST:-127.0.0.1}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE:-laravel}
DB_USERNAME=${DB_USERNAME:-root}
DB_PASSWORD=${DB_PASSWORD}
MYSQL_ATTR_SSL_CA=${SSL_CA_PATH}

LMS_ADMIN_EMAIL=${LMS_ADMIN_EMAIL:-admin@lms.local}
LMS_ADMIN_PASSWORD=${LMS_ADMIN_PASSWORD:-password}

BROADCAST_DRIVER=${BROADCAST_DRIVER:-log}
CACHE_DRIVER=${CACHE_DRIVER:-file}
FILESYSTEM_DISK=${FILESYSTEM_DISK:-local}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-sync}
SESSION_DRIVER=${SESSION_DRIVER:-file}
SESSION_LIFETIME=${SESSION_LIFETIME:-120}

VOICERSS_API_KEY=${VOICERSS_API_KEY}

MAIL_MAILER=${MAIL_MAILER:-smtp}
MAIL_HOST=${MAIL_HOST:-localhost}
MAIL_PORT=${MAIL_PORT:-587}
MAIL_USERNAME=${MAIL_USERNAME}
MAIL_PASSWORD=${MAIL_PASSWORD}
MAIL_ENCRYPTION=${MAIL_ENCRYPTION:-tls}
MAIL_FROM_ADDRESS="${MAIL_FROM_ADDRESS:-hello@example.com}"
MAIL_FROM_NAME="${APP_NAME:-LMS}"
EOF

echo "[entrypoint] .env written."

# --- Clear config cache so Laravel reads fresh .env ---
php artisan config:clear
php artisan cache:clear

# --- Run database migrations ---
echo "[entrypoint] Running database migrations..."
php artisan migrate --force

# --- Create storage symlink ---
if [ ! -L "$APP_DIR/public/storage" ]; then
    echo "[entrypoint] Creating storage symlink..."
    php artisan storage:link
fi

echo "[entrypoint] Startup complete. Launching Apache..."
exec "$@"
