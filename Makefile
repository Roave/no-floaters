.PHONY: install qa cs phpstan tests mutation-tests

install:
	composer update

qa: phpstan cs

cs:
	vendor/bin/phpcs

phpstan:
	vendor/bin/phpstan analyse

tests:
	vendor/bin/phpunit

mutation-tests:
	vendor/bin/infection --min-msi=100 --min-covered-msi=100
