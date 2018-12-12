FROM php:7.2-fpm-stretch

ARG WEB_USER_ID=33
ARG WEB_USER_NAME=www-data

RUN apt-get update

# Install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
  && php composer-setup.php --install-dir=/usr/bin --filename=composer \
  && php -r "unlink('composer-setup.php');"
# Create dir for composer cache
RUN mkdir /var/www/.composer && chown "${WEB_USER_ID}" /var/www/.composer

RUN apt-get install git unzip -y

RUN echo "Building for web user: id=${WEB_USER_ID} name=${WEB_USER_NAME}"
RUN useradd -m -u ${WEB_USER_ID} ${WEB_USER_NAME} || echo "User exists, it's ok."
RUN sed -i -- "s/user = www-data/user = ${WEB_USER_NAME}/g" /usr/local/etc/php-fpm.d/www.conf
RUN echo "listen = /sock/docker.sock" >> /usr/local/etc/php-fpm.d/zz-docker.conf
RUN echo "listen.mode = 0666" >> /usr/local/etc/php-fpm.d/zz-docker.conf


RUN docker-php-ext-configure opcache --enable-opcache \
  && docker-php-ext-install opcache


USER ${WEB_USER_ID}
