pwd := $(shell pwd)
composer := ./composer

.PHONY: \
	phpcs \
	clean \
	docker-bash \
	use-uopz \
	use-runkit \
	test \
	test-uopz \
	test-runkit \
	ci-php-7.3-runkit-4.0.0a6 \
	ci-php-7.3-runkit-3.0.0 \
	ci-php-7.3-runkit-2.1.0 \
	ci-php-7.3-runkit-1.0.11 \
	ci-php-7.4-runkit-4.0.0a6 \
	ci-php-8.0-runkit-4.0.0a6 \
	ci-php-8.1-runkit-4.0.0a6 \
	ci-php-7.3-uopz-6.1.2 \
	ci-php-7.4-uopz-6.1.2 \
	ci-php-8.0-uopz \
	ci-php-8.1-uopz

$(composer):
	curl -Ls https://raw.githubusercontent.com/tototoshi/composerx/main/composer > $(composer) && chmod +x $(composer)

clean:
	rm -rf vendor/
	$(composer) clear-cache

tools/php-cs-fixer/vendor/bin/php-cs-fixer:
	mkdir -p tools/php-cs-fixer
	$(composer) require --dev --working-dir=tools/php-cs-fixer friendsofphp/php-cs-fixer

phpcs: tools/php-cs-fixer/vendor/bin/php-cs-fixer
	./tools/php-cs-fixer/vendor/bin/php-cs-fixer fix src
	./tools/php-cs-fixer/vendor/bin/php-cs-fixer fix test	
	
phpcs-check:
	./tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --dry-run src
	./tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --dry-run test

docker-bash:
	docker build -t staticmock-dev -f Dockerfile .
	docker run -it --rm -v $(pwd):$(pwd) -w $(pwd) staticmock-dev bash

use-uopz:
	echo "extension=uopz.so" > $(PHP_INI_DIR)/conf.d/staticmock.ini

use-runkit7:
	echo "extension=runkit7.so" > $(PHP_INI_DIR)/conf.d/staticmock.ini

test:
	$(composer) exec phpunit test

test-uopz: use-uopz test

test-runkit7: use-runkit7 test

ci-php-7.3-runkit-4.0.0a6:
	docker run --rm -v $(pwd):$(pwd) -w $(pwd) php:7.3 ./ci/test-runkit.sh 4.0.0a6

ci-php-7.3-runkit-3.0.0:
	docker run --rm -v $(pwd):$(pwd) -w $(pwd) php:7.3 ./ci/test-runkit.sh 3.0.0

ci-php-7.3-runkit-2.1.0:
	docker run --rm -v $(pwd):$(pwd) -w $(pwd) php:7.3 ./ci/test-runkit.sh 2.1.0

ci-php-7.3-runkit-1.0.11:
	docker run --rm -v $(pwd):$(pwd) -w $(pwd) php:7.3 ./ci/test-runkit.sh 1.0.11

ci-php-7.4-runkit-4.0.0a6:
	docker run --rm -v $(pwd):$(pwd) -w $(pwd) php:7.4 ./ci/test-runkit.sh 4.0.0a6

ci-php-8.0-runkit-4.0.0a6:
	docker run --rm -v $(pwd):$(pwd) -w $(pwd) php:8.0 ./ci/test-runkit.sh 4.0.0a6

ci-php-8.1-runkit-4.0.0a6:
	docker run --rm -v $(pwd):$(pwd) -w $(pwd) php:8.1 ./ci/test-runkit.sh 4.0.0a6

ci-php-7.3-uopz-6.1.2:
	docker run --rm -v $(pwd):$(pwd) -w $(pwd) php:7.3 ./ci/test-uopz.sh 6.1.2

ci-php-7.4-uopz-6.1.2:
	docker run --rm -v $(pwd):$(pwd) -w $(pwd) php:7.4 ./ci/test-uopz.sh 6.1.2

ci-php-8.0-uopz:
	docker run --rm -v $(pwd):$(pwd) -w $(pwd) php:8.0 ./ci/test-uopz-master.sh

ci-php-8.1-uopz:
	docker run --rm -v $(pwd):$(pwd) -w $(pwd) php:8.1 ./ci/test-uopz-master.sh
