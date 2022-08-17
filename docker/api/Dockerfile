# https://hub.docker.com/_/php
FROM php:8.1-fpm-buster
COPY docker/api/php.ini /usr/local/etc/php/
COPY docker/api/start.sh /var/www/
RUN chmod 744 /var/www/start.sh

# setting working directory to source code root
WORKDIR /var/www/html

# 各種パッケージ追加
RUN apt-get update && \
  apt-get -y install --no-install-recommends git unzip libzip-dev libicu-dev libonig-dev && \
  apt-get clean && \
  rm -rf /var/lib/apt/lists/* && \
  docker-php-ext-install intl pdo_mysql zip bcmath

  # timezoneをAsia/Tokyoに設定する
RUN cp /usr/share/zoneinfo/Asia/Tokyo /etc/localtime

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV TZ=Asia/Tokyo

EXPOSE 8000

CMD ["../start.sh"]