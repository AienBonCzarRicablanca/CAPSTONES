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

# Copy composer files first (layer cache optimisation)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copy package files and install/build frontend assets
COPY package.json package-lock.json* ./
RUN npm ci --ignore-scripts

# Copy the rest of the application
COPY . .

# Build frontend assets (Vite)
RUN npm run build

# Run composer post-install scripts after full copy
RUN composer dump-autoload --no-dev --optimize

# Fix storage & bootstrap/cache permissions
RUN chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache \
    && chmod -R 775 \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

# Copy and set up the Docker entrypoint (dos2unix fixes CRLF if any)
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN dos2unix /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["apache2-foreground"]
