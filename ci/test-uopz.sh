#!/bin/bash

set -eux

export PATH=$HOME/bin:$PATH

uopz_version=$1

$(dirname $0)/install_packages.sh
$(dirname $0)/install_uopz.sh $uopz_version

./composer install
./composer update
./composer exec phpunit test
