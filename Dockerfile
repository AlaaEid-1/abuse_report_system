# ==============================================================================
# Stage 1: Build Frontend Assets (Vite + Tailwind CSS + Livewire)
# ==============================================================================
FROM node:20-alpine AS node_builder

WORKDIR /app

# Copy package descriptors
COPY package*.json ./

# Install Node dependencies
RUN npm ci || npm install

# Copy application source files required for asset compilation
COPY vite.config.js postcss.config.js tailwind.config.js ./
COPY resources/ ./resources/
COPY public/ ./public/

# Build production assets into public/build
RUN npm run build


# ==============================================================================
# Stage 2: Production PHP 8.4 + Apache Application Image
# ==============================================================================
FROM php:8.4-apache AS production

LABEL maintainer="SafeVoice Platform <support@safevoice.org>"

# Set working directory
WORKDIR /var/www/html

# Install required system dependencies and libraries
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# Configure and install required PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mysqli \
        mbstring \
        intl \
        zip \
        bcmath \
        gd \
        opcache

# Install Composer globally from official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache mod_rewrite for Laravel routing
RUN a2enmod rewrite

# Configure Apache DocumentRoot to point to /var/www/html/public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/apache2.conf \
    && printf "<Directory /var/www/html/public>\n\tOptions Indexes FollowSymLinks\n\tAllowOverride All\n\tRequire all granted\n</Directory>\n" >> /etc/apache2/sites-available/000-default.conf

# Configure production OPcache settings
RUN printf "opcache.enable=1\n\
opcache.enable_cli=1\n\
opcache.memory_consumption=128\n\
opcache.interned_strings_buffer=8\n\
opcache.max_accelerated_files=10000\n\
opcache.revalidate_freq=2\n\
opcache.validate_timestamps=1\n" > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Set PHP memory limit and upload size limits for evidence uploads
RUN printf "memory_limit=256M\n\
upload_max_filesize=20M\n\
post_max_size=25M\n" > /usr/local/etc/php/conf.d/custom-php.ini

# Copy full application source code
COPY . .

# Copy compiled frontend assets from Stage 1
COPY --from=node_builder /app/public/build ./public/build

# Install Composer dependencies for production (excluding dev dependencies)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Create required storage directories and set permissions for www-data
RUN mkdir -p storage/app/private/evidence \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copy entrypoint script and make it executable
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose HTTP port
EXPOSE 80

# Use production entrypoint
ENTRYPOINT ["docker-entrypoint.sh"]

# Start Apache web server in foreground
CMD ["apache2-foreground"]
