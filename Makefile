USER_ID ?= $(shell id -u)
GROUP_ID ?= $(shell id -g)
COMPOSER_HOME ?= $(HOME)/.composer

.PHONY: help
help:
	@echo "Please use 'make <target>' where <target> is one of"
	@grep -E '^\.PHONY: [a-zA-Z_-]+ .*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = "(: |##)"}; {printf "\033[36m%-30s\033[0m %s\n", $$2, $$3}'

.PHONY: composer  ## Use composer in Docker
composer:
	docker run --rm --interactive --tty \
		--env COMPOSER_ALLOW_SUPERUSER=0 \
  		--volume $(PWD):/app \
  		--volume $(COMPOSER_HOME):/tmp \
  		--user $(USER_ID):$(GROUP_ID) \
  		composer:2 sh

.PHONY: tests ## Run test using Docker
tests:
	docker run --rm --interactive --tty \
		--volume $(PWD):/app \
		--user $(USER_ID):$(GROUP_ID) \
		--workdir /app \
		php:8-cli-alpine php ./vendor/bin/phpunit .