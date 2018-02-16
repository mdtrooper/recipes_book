FROM php:7.0-apache
RUN apt-get update && apt-get install -y \
    wget \
    git

COPY docker/composer_installer.sh /
RUN sh /composer_installer.sh
