#!/bin/bash

set -eux

version=$1

cd /
git clone https://github.com/runkit7/runkit7.git
cd runkit7
git checkout $version
phpize
./configure
make
make install

echo "extension=/runkit7/modules/runkit7.so" >> $PHP_INI_DIR/conf.d/runkit.ini
