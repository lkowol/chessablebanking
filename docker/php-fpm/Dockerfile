FROM php:8.0-fpm

RUN printf '[PHP]\ndate.timezone = "Europe/Warsaw"\n' > /usr/local/etc/php/conf.d/tzone.ini

RUN apt-get update && apt-get install -y \
    wget nano supervisor \
    libssh-dev git libzip-dev zip
RUN pecl install apcu

RUN docker-php-ext-install opcache pdo_mysql bcmath zip

RUN echo 'memory_limit = 1G' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini;

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/
COPY . /var/www
COPY ./docker/php-fpm/wait-for-it.sh /usr/bin/wait-for-it

RUN composer install -o
RUN PATH=$PATH:/var/www/vendor/bin:bin
RUN ["mkdir", "-p", "/var/www/var/cache/prod"]
RUN ["mkdir", "-p", "/var/www/var/cache/dev"]
RUN ["touch", "/var/www/var/log/prod.log"]
RUN ["touch", "/var/www/var/log/dev.log"]
RUN ["chmod", "-R", "777", "/var/www/var"]
RUN ["chmod", "777", "/usr/bin/wait-for-it"]

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo "Europe/Warsaw" > /etc/timezone
RUN echo 'alias pbc="php /var/www/bin/console"' >> ~/.bashrc

CMD composer install -o;

COPY ./docker/php-fpm/supervisord-php-fpm.conf /etc/supervisor/supervisord-php-fpm.conf