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

if [[ $version < "3.0.0" ]]; then
    extension_name="runkit"
else
    extension_name="runkit7"
fi

echo "extension=/runkit7/modules/${extension_name}.so" >> $PHP_INI_DIR/conf.d/runkit.ini
