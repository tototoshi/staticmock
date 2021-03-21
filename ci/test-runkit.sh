#!/bin/bash

set -eux

runkit_version=$1

export PATH=$HOME/bin:$PATH

$(dirname $0)/install_packages.sh
$(dirname $0)/install_composer.sh
$(dirname $0)/install_runkit.sh $runkit_version

composer install
composer update

./vendor/bin/phpunit test
