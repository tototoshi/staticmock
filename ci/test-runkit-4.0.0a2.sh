#!/bin/bash

set -eux

export PATH=$HOME/bin:$PATH

$(dirname $0)/install_packages.sh
$(dirname $0)/install_composer.sh
$(dirname $0)/install_runkit7.sh 4.0.0a2

composer install

./vendor/bin/phpunit
