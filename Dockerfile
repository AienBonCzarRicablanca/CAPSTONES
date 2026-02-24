# ---------------------------------------------------------------
# Laravel on Render.com with Aiven MySQL
# ---------------------------------------------------------------
FROM php:8.1-apache

# Install system dependencies + dos2unix to fix Windows line endings
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    dos2unix \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions required by Laravel
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    xml

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install Node.js 20 (for building Vite/frontend assets)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configure Apache - enable mod_rewrite and set DocumentRoot to /public
RUN a2enmod rewrite
COPY docker-vhost.conf /etc/apache2/sites-available/000-default.conf

# Set working directory
WORKDIR /var/www/html

# --- Layer cache: install PHP deps ---
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction

# --- Layer cache: install Node deps (npm ci needs postinstall for esbuild/vite) ---
COPY package.json package-lock.json* ./
RUN npm ci

# --- Copy full application source ---
COPY . .

# Regenerate optimized autoloader now that all app files are present
RUN composer dump-autoload --no-dev --optimize --no-scripts

# Create a minimal .env so Vite doesn't complain during build
# (real secrets come from Render env vars at runtime)
RUN printf 'APP_NAME=LMS\nAPP_ENV=production\nAPP_KEY=base64:aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa=\nAPP_URL=http://localhost\nDB_CONNECTION=mysql\n' > .env

# Build frontend assets (Vite)
RUN npm run build

# Remove the placeholder .env — the real one comes from Render env vars
RUN rm -f .env

# Fix storage & bootstrap/cache permissions
RUN chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache \
    && chmod -R 775 \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

# Copy entrypoint, fix line endings, make executable
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN dos2unix /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["apache2-foreground"]
CMD ["apache2-foreground"]
