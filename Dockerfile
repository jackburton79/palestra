FROM php:8.1-apache-bookworm
MAINTAINER Stefano Ceccherini <stefano.ceccherini@proton.me>

RUN apt-get update && apt-get install -y locales
RUN echo 'it_IT.UTF-8 UTF-8' >> /etc/locale.gen
RUN dpkg-reconfigure --frontend noninteractive locales
RUN locale-gen it_IT.UTF-8
ENV LANG POSIX
ENV LC_CTYPE it_IT.UTF-8
RUN apt-get update && \
	docker-php-ext-install pdo pdo_mysql	

COPY ./server /var/www/html/

EXPOSE 80/tcp
