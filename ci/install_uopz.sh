#!/bin/bash

set -eux

pecl install uopz

echo "extension=uopz.so" > $PHP_INI_DIR/conf.d/uopz.ini
