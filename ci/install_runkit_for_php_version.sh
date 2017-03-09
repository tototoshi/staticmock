#!/bin/bash -xeu
# This script
PHP_MAJOR_VERSION=$(php -r "echo PHP_MAJOR_VERSION;");

if expr "$PHP_MAJOR_VERSION" ">=" "7" ; then
	git clone --depth 1 https://github.com/runkit7/runkit7.git
	pushd runkit7
	phpize && ./configure && make && make install || exit 1
	echo 'extension = runkit.so' >> ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/travis.ini
	popd
else
	pecl install runkit
fi
