pwd := $(shell pwd)

.PHONY: \
	clean \
	test-all \
	test-php-7.3-runkit-4.0.0a2 \
	test-php-7.3-runkit-3.0.0 \
	test-php-7.3-runkit-2.1.0 \
	test-php-7.3-runkit-1.0.11 \
	test-php-7.4-runkit-4.0.0a2 \
	test-php-8.0-runkit-4.0.0a2

clean:
	rm -rf vendor/
	composer clear-cache

test-php-7.3-runkit-4.0.0a2:
	docker run --rm -v $(pwd):$(pwd) -w $(pwd) php:7.3-buster ./ci/test-runkit-4.0.0a2.sh

test-php-7.3-runkit-3.0.0:
	docker run --rm -v $(pwd):$(pwd) -w $(pwd) php:7.3-buster ./ci/test-runkit-3.0.0.sh

test-php-7.3-runkit-2.1.0:
	docker run --rm -v $(pwd):$(pwd) -w $(pwd) php:7.3-buster ./ci/test-runkit-2.1.0.sh

test-php-7.3-runkit-1.0.11:
	docker run --rm -v $(pwd):$(pwd) -w $(pwd) php:7.3-buster ./ci/test-runkit-1.0.11.sh

test-php-7.4-runkit-4.0.0a2:
	docker run --rm -v $(pwd):$(pwd) -w $(pwd) php:7.4-buster ./ci/test-runkit-4.0.0a2.sh

test-php-8.0-runkit-4.0.0a2:
	docker run --rm -v $(pwd):$(pwd) -w $(pwd) php:8.0-buster ./ci/test-runkit-4.0.0a2.sh
