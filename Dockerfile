FROM php:8.1-apache-bookworm
MAINTAINER Stefano Ceccherini <stefano.ceccherini@proton.me>

ARG db_user
ARG db_password
ARG db_host
ARG db_name

RUN a2enmod rewrite
RUN apt-get update && apt-get install -y locales git unzip
RUN echo 'it_IT.UTF-8 UTF-8' >> /etc/locale.gen
RUN dpkg-reconfigure --frontend noninteractive locales
RUN locale-gen it_IT.UTF-8

RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/apache2.conf
    
ENV LANG POSIX
ENV LC_CTYPE it_IT.UTF-8
RUN apt-get update && \
	docker-php-ext-install pdo pdo_mysql

COPY ./src /var/www/html/

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --working-dir=/var/www/html/public require slim/slim slim/psr7 slim/http

RUN sed -i s/DUMMYUSER/${db_user}/ /var/www/html/Config/config.php
RUN sed -i s/DUMMYPASS/${db_password}/ /var/www/html/Config/config.php
RUN sed -i s/DUMMYHOST/${db_host}/ /var/www/html/Config/config.php
RUN sed -i s/DUMMYNAME/${db_name}/ /var/www/html/Config/config.php

EXPOSE 80/tcp
