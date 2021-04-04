#!/bin/bash

set -eux

cd /
git clone https://github.com/krakjoe/uopz.git
cd uopz
phpize
./configure
make
make install

echo "extension=uopz.so" > $PHP_INI_DIR/conf.d/uopz.ini
