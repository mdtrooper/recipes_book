FROM php:7-apache
RUN apt-get update && apt-get install -y \
    wget \
    git \
    zip \
    unzip \
    vim \
    acl

RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

COPY docker/composer_installer.sh /
RUN sh /composer_installer.sh
RUN cd /bin ; wget -O phpunit https://phar.phpunit.de/phpunit-7.phar; chmod +x phpunit

RUN chmod g+s /var/www/html -R
RUN setfacl -R -d -m u:1000:rwx -m g:1000:rwx  .
