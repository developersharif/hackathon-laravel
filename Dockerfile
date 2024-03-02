FROM php:8.2.12-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install system dependencies, PHP, and PECL extensions
RUN apt-get update -y && apt-get install -y \
    libicu-dev \
    libmariadb-dev \
    unzip zip \
    zlib1g-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    && pecl install grpc \
    && docker-php-ext-enable grpc \
    && docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg \
    && docker-php-ext-install gettext intl pdo_mysql gd

# Install Node.js
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash - \
    && apt-get install -y nodejs

# Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy the project files to the container
COPY . .

EXPOSE 6001

USER root
