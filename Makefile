export USER_ID := $(shell id -u)
export GROUP_ID := $(shell id -g)

.PHONY: *
up:
	env USER_ID=$(USER_ID) GROUP_ID=$(GROUP_ID) docker compose up -d
build:
	env USER_ID=$(USER_ID) GROUP_ID=$(GROUP_ID) docker compose up -d --build
down:
	env USER_ID=$(USER_ID) GROUP_ID=$(GROUP_ID) docker compose down --remove-orphans
restart: down up

test:
	env USER_ID=$(USER_ID) GROUP_ID=$(GROUP_ID) docker compose exec php bin/phpunit

fixtures:
	env USER_ID=$(USER_ID) GROUP_ID=$(GROUP_ID) docker compose exec php bin/console doctrine:schema:drop --force
	env USER_ID=$(USER_ID) GROUP_ID=$(GROUP_ID) docker compose exec php bin/console doctrine:schema:update --force
	env USER_ID=$(USER_ID) GROUP_ID=$(GROUP_ID) docker compose exec php bin/console doctrine:fixtures:load --no-interaction

migrate:
	env USER_ID=$(USER_ID) GROUP_ID=$(GROUP_ID) docker compose exec php bin/console doctrine:schema:update --force

prepare-env:
	cp -n .env.dev .env

composer-install:
	env USER_ID=$(USER_ID) GROUP_ID=$(GROUP_ID) docker compose exec php composer install

generate-keypair:
	env USER_ID=$(USER_ID) GROUP_ID=$(GROUP_ID) docker compose exec php bin/console lexik:jwt:generate-keypair --skip-if-exists

init: prepare-env up composer-install fixtures generate-keypair
