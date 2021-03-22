pwd := $(shell pwd)
composer := ./composer

.PHONY: \
	clean \
	docker-bash \
	test \
	ci-php-7.3-runkit-4.0.0a2 \
	ci-php-7.3-runkit-3.0.0 \
	ci-php-7.3-runkit-2.1.0 \
	ci-php-7.3-runkit-1.0.11 \
	ci-php-7.4-runkit-4.0.0a2 \
	ci-php-8.0-runkit-4.0.0a2

$(composer):
	curl -Ls https://raw.githubusercontent.com/tototoshi/composerx/main/composer > $(composer) && chmod +x $(composer)

clean:
	rm -rf vendor/
	$(composer) clear-cache

docker-bash:
	docker build -t staticmock-dev -f Dockerfile .
	docker run -it --rm -v $(pwd):$(pwd) -w $(pwd) staticmock-dev bash

test:
	$(composer) exec phpunit test

ci-php-7.3-runkit-4.0.0a2:
	docker run --rm -v $(pwd):$(pwd) -w $(pwd) php:7.3 ./ci/test-runkit.sh 4.0.0a2

ci-php-7.3-runkit-3.0.0:
	docker run --rm -v $(pwd):$(pwd) -w $(pwd) php:7.3 ./ci/test-runkit.sh 3.0.0

ci-php-7.3-runkit-2.1.0:
	docker run --rm -v $(pwd):$(pwd) -w $(pwd) php:7.3 ./ci/test-runkit.sh 2.1.0

ci-php-7.3-runkit-1.0.11:
	docker run --rm -v $(pwd):$(pwd) -w $(pwd) php:7.3 ./ci/test-runkit.sh 1.0.11

ci-php-7.4-runkit-4.0.0a2:
	docker run --rm -v $(pwd):$(pwd) -w $(pwd) php:7.4 ./ci/test-runkit.sh 4.0.0a2

ci-php-8.0-runkit-4.0.0a2:
	docker run --rm -v $(pwd):$(pwd) -w $(pwd) php:8.0 ./ci/test-runkit.sh 4.0.0a2
