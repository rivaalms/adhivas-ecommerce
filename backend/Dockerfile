FROM php:8.3-cli-alpine

# Install system dependencies and PHP extensions
RUN apk update && apk add --no-cache \
    postgresql-dev \
    git \
    unzip \
    zip \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    && docker-php-ext-install \
    pdo_pgsql \
    pgsql \
    zip \
    intl \
    bcmath \
    mbstring

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Expose port 8000
EXPOSE 8000

# Make entrypoint executable in-image and set it
RUN chmod +x docker-entrypoint.sh
ENTRYPOINT ["/var/www/html/docker-entrypoint.sh"]
