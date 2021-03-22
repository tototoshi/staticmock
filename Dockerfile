FROM php:latest

RUN apt-get update &&\
    apt-get install -y git unzip zip

RUN pecl install channel://pecl.php.net/runkit7-4.0.0a2 &&\
    echo "extension=runkit7.so" > $PHP_INI_DIR/conf.d/runkit.ini

ENV PATH=/root/bin:$PATH

CMD "/bin/bash"
