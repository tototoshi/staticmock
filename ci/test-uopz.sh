#!/bin/bash

set -eux

export PATH=$HOME/bin:$PATH

$(dirname $0)/install_packages.sh
$(dirname $0)/install_uopz.sh

./composer install
./composer update
./composer exec phpunit test
