FROM php:7.4

RUN apt-get update &&\
    apt-get install -y git unzip zip

RUN cd /tmp &&\
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" &&\
    php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" &&\
    mkdir -p $HOME/bin &&\
    php composer-setup.php --install-dir=$HOME/bin --filename=composer

RUN pecl install channel://pecl.php.net/runkit7-4.0.0a2

RUN pecl install uopz

ENV PATH=/root/bin:$PATH

CMD "/bin/bash"
