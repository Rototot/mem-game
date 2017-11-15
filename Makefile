build-dev: composer-install-dev env-dev migrate-up

build-prod: composer-install-prod env-prod migrate-up

composer-install:
	composer install

composer-install-dev:
	composer install

composer-install-prod:
	composer install

env-prod:
	php init --env=Production --overwrite=All

env-dev:
	php init --env=Development --overwrite=All

migrate-up:
	php yii migrate --interactive=0
