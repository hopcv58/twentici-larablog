dir=${CURDIR}
export COMPOSE_PROJECT_NAME=lara-blog

ifndef APP_ENV
	# Determine if .env file exist
	ifneq ("$(wildcard .env)","")
		include .env
	endif
endif

laravel_user=-u www-data
project=-p ${COMPOSE_PROJECT_NAME}
service=${COMPOSE_PROJECT_NAME}:latest
ifeq ($(GITLAB_CI),1)
	# Determine additional params for phpunit in order to generate coverage badge on GitLabCI side
	phpunitOptions=--coverage-text --colors=never
endif

build:
	@docker-compose -f docker-compose.yml build

start:
	@docker-compose -f docker-compose.yml $(project) up -d

stop:
	@docker-compose -f docker-compose.yml $(project) down

restart: stop start

ssh:
	@docker-compose $(project) exec $(optionT) $(laravel_user) laravel bash

ssh-mysql:
	@docker-compose $(project) exec mysql bash

exec:
	@docker-compose $(project) exec $(optionT) $(laravel_user) laravel $$cmd

exec-bash:
	@docker-compose $(project) exec $(optionT) $(laravel_user) laravel bash -c "$(cmd)"

exec-by-root:
	@docker-compose $(project) exec $(optionT) laravel $$cmd

composer-install-no-dev:
	@make exec-bash cmd="COMPOSER_MEMORY_LIMIT=-1 composer install --optimize-autoloader --no-dev"

composer-install:
	@make exec-bash cmd="COMPOSER_MEMORY_LIMIT=-1 composer install --optimize-autoloader"

composer-update:
	@make exec-bash cmd="COMPOSER_MEMORY_LIMIT=-1 composer update"

key-generate:
	@make exec-bash cmd="php artisan key:generate"

info:
	@make exec-bash cmd="php artisan --version"
	@make exec-bash cmd="php artisan env"
	@make exec-bash cmd="php --version"

logs:
	@docker logs -f ${COMPOSE_PROJECT_NAME}_laravel

logs-supervisord:
	@docker logs -f ${COMPOSE_PROJECT_NAME}_supervisord

logs-mysql:
	@docker logs -f ${COMPOSE_PROJECT_NAME}_mysql

drop-migrate:
	@make exec-bash cmd="php artisan migrate:fresh"

migrate:
	@make exec-bash cmd="php artisan migrate --force"

seed:
	@make exec-bash cmd="php artisan db:seed --force"

all:
	@make build
	@make start
	@make composer-install
	@make migrate
	@make seed
