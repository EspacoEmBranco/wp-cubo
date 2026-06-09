FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
  libpng-dev \
  libjpeg-dev \
  libwebp-dev \
  libzip-dev \
  libxml2-dev \
  libcurl4-openssl-dev \
  libonig-dev \
  libmagickwand-dev \
  && docker-php-ext-configure gd --with-jpeg --with-webp \
  && docker-php-ext-install \
  mysqli \
  mbstring \
  xml \
  zip \
  curl \
  gd \
  intl \
  exif \
  opcache \
  && pecl install imagick \
  && docker-php-ext-enable imagick \
  && rm -rf /var/lib/apt/lists/* \
  && curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
  && chmod +x wp-cli.phar \
  && mv wp-cli.phar /usr/local/bin/wp
