#!/bin/bash

set -eux

export PATH=$HOME/bin:$PATH

$(dirname $0)/install_packages.sh
$(dirname $0)/install_uopz_master.sh

./composer install
./composer exec phpunit test
