FROM php:8.1-apache

RUN apt-get update && apt-get -y install software-properties-common git unzip bc curl libxslt-dev libpcre3-dev openssl make libssl-dev libffi-dev gcc libxml2-dev apt-transport-https zlib1g-dev  libcurl4-openssl-dev  libzip-dev zip libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libedit-dev && \
    a2enmod rewrite && \
    a2enmod status && \
    a2enmod expires && \
    a2enmod ssl && \
    a2enmod proxy && \
    a2enmod proxy_http && \
    curl --silent https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer 

RUN apt-get install -y libyaml-dev && \
    pecl install yaml-2.2.2

RUN echo "extension=yaml.so" > $PHP_INI_DIR/conf.d/20-yaml.ini

RUN docker-php-ext-install zip gd 
RUN docker-php-ext-configure intl && docker-php-ext-install intl;

RUN apt-get install -y libpq-dev \
    && docker-php-ext-install pdo mysqli pdo_mysql pcntl


RUN apt-get install -y build-essential gcc g++ make ffmpeg

RUN pecl install redis && docker-php-ext-enable redis

RUN \
    a2enmod rewrite && \
    a2enmod status && \
    a2enmod expires && \
    a2enmod proxy && \
    a2enmod proxy_http && \
    rm /etc/apache2/sites-enabled/00* && \
    apt-get remove -y bc libxslt-dev libpcre3-dev make libssl-dev libffi-dev gcc libxml2-dev apt-transport-https zlib1g-dev  libcurl4-openssl-dev  libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libedit-dev && \
    adduser --disabled-password --gecos "" brisk && \
    mkdir /var/log/brisk.http && \
    chown brisk.brisk /var/log/brisk.http && \
    rm -rf /var/lib/apt/lists/*

COPY config/sites-enabled/brisk.http.conf /etc/apache2/sites-enabled/
COPY config/apache2.conf /etc/apache2/
COPY config/ports.conf /etc/apache2/

WORKDIR /home/brisk.http
