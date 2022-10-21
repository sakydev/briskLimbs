# Local Docker
DOCKER_COMPOSE_CMD ?= docker compose -f docker-compose.yml -f docker-compose.dev.yml

.PHONY: help
help:                                                                           ## Shows this help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_\-\.]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

.PHONY: setup
setup: build start copy-env composer-install key-generate npm-install

.PHONY: build
build:																			## Build containers
	$(DOCKER_COMPOSE_CMD) build

.PHONY: start
start:																			## Start containers
	$(DOCKER_COMPOSE_CMD) up -d --remove-orphans

.PHONY: stop
stop:																			## Stop containers
	$(DOCKER_COMPOSE_CMD) stop

.PHONY: restart
restart: stop start																## Restart containers

.PHONY: down
down:																			## Stop and remove containers
	$(DOCKER_COMPOSE_CMD) down

.PHONY: http
http:																		## Run Laravel container
	$(DOCKER_COMPOSE_CMD) exec brisk.http bash

.PHONY: mysql
mysql:																		## Run Mysql container
	docker exec -it brisk.mysql bash

.PHONY: redis
redis:																		## Run Redis container
	docker exec -it brisk.redis bash

.PHONY: logs
logs:																		    ## Show php container logs
	$(DOCKER_COMPOSE_CMD) logs --follow php

.PHONY: copy-env
copy-env:																	    ## Copy .env.example to .env
	$(DOCKER_COMPOSE_CMD) exec -T php cp .env.example .env

.PHONY: key-generate
key-generate:																	## Run laravel key generation
	$(DOCKER_COMPOSE_CMD) exec -T php php artisan key:generate

.PHONY: composer-install
composer-install:																## Run composer install
	$(DOCKER_COMPOSE_CMD) exec -T php composer install

.PHONY: npm-install
npm-install:																	## Run npm install
	$(DOCKER_COMPOSE_CMD) exec -T php npm install
	$(DOCKER_COMPOSE_CMD) exec -T php npm run dev

.PHONY: migrate
migrate:																		## Migrations
	$(DOCKER_COMPOSE_CMD) exec -T php php artisan migrate

.PHONY: migrate-fresh
migrate-fresh:																	## Clear DB and migrate from scratch
	$(DOCKER_COMPOSE_CMD) exec -T php php artisan migrate:fresh --seed

.PHONY: clear-cache
clear-cache:																	## Clear all cache
	$(DOCKER_COMPOSE_CMD) exec -T php php artisan optimize:clear

.PHONY: seed
seed:																			## Seeds
	$(DOCKER_COMPOSE_CMD) exec -T php php artisan db:seed

#.PHONY: test
#test:									 										## Execute tests
#	$(DOCKER_COMPOSE_CMD) exec -T php php -dxdebug.mode=0 -dxdebug.start_with_request=0 vendor/bin/phpunit --colors=always -d memory_limit=512M

.PHONY: delete-merged-branches
delete-merged-branches:															## Delete local merged branches
	git branch --merged | grep -v \* | xargs git branch -D