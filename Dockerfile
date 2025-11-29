FROM php:8.3-fpm

USER root

# Dependências + mongodb extension
RUN apt-get update && apt-get install -y \
        git \
        unzip \
        wget \
        pkg-config \
        libssl-dev \
        libmongocrypt0 \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Symfony CLI
RUN wget https://get.symfony.com/cli/installer -O - | bash \
    && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony \
    && chmod +x /usr/local/bin/symfony

# Fix libmongocrypt symlink
RUN ln -s /usr/lib/x86_64-linux-gnu/libmongocrypt.so.0 \
          /usr/lib/x86_64-linux-gnu/libmongocrypt.so \
          || true

WORKDIR /var/www

# Ajuste UID/GID
RUN usermod -u 1000 www-data && \
    groupmod -g 1000 www-data

USER www-data
