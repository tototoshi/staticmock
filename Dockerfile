FROM php:8.4

RUN apt-get update &&\
    apt-get install -y git unzip zip

# runkit7 does not support PHP >= 8.2. Use uopz (built from master) instead.
RUN cd /tmp &&\
    git clone https://github.com/krakjoe/uopz.git &&\
    cd uopz &&\
    phpize && ./configure && make && make install &&\
    rm -rf /tmp/uopz

ENV PATH=/root/bin:$PATH

CMD "/bin/bash"
