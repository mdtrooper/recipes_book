FROM php:7-apache
RUN apt-get update && apt-get install -y \
    wget \
    git \
    zip \
    unzip \
    vim

COPY docker/composer_installer.sh /
RUN sh /composer_installer.sh
RUN cd /bin ; wget -O phpunit https://phar.phpunit.de/phpunit-7.phar; chmod +x phpunit
