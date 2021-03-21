#!/bin/bash

set -eux

export PATH=$HOME/bin:$PATH

$(dirname $0)/install_packages.sh
$(dirname $0)/install_composer.sh
$(dirname $0)/install_uopz.sh

composer install
composer update

./vendor/bin/phpunit test
