#!/bin/bash

set -eux

version=$1

pecl install uopz-$version

echo "extension=uopz.so" > $PHP_INI_DIR/conf.d/uopz.ini
