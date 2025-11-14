FROM php:8.1-apache-bookworm
MAINTAINER Stefano Ceccherini <stefano.ceccherini@proton.me>

ARG db_user
ARG db_password
ARG db_host
ARG db_name

RUN apt-get update && apt-get install -y locales
RUN echo 'it_IT.UTF-8 UTF-8' >> /etc/locale.gen
RUN dpkg-reconfigure --frontend noninteractive locales
RUN locale-gen it_IT.UTF-8

ENV LANG POSIX
ENV LC_CTYPE it_IT.UTF-8
RUN apt-get update && \
	docker-php-ext-install pdo pdo_mysql	

COPY ./server /var/www/html/
COPY ./index.html /var/www/html/
COPY ./css /var/www/html/css/
COPY ./js /var/www/html/js/

RUN sed -i s/DUMMYUSER/${db_user}/ /var/www/html/inc/config.php
RUN sed -i s/DUMMYPASS/${db_password}/ /var/www/html/inc/config.php
RUN sed -i s/DUMMYHOST/${db_host}/ /var/www/html/inc/config.php
RUN sed -i s/DUMMYNAME/${db_name}/ /var/www/html/inc/config.php

EXPOSE 80/tcp
