.PHONY: config_common composer_install docker_reqs_up tests clean down start run bash 

config_common:
	cp -f common.env.dist common.env
	mkdir -p var/logs var/sessions var/cache/dev && touch var/logs/dev.log && (chmod 777 -Rf var/logs var/sessions var/cache || true) && (chown -Rf www-data:www-data var/logs var/sessions var/cache || true)

composer_install:
	composer install --ignore-platform-reqs --no-scripts

docker_reqs_up:
	docker-compose up -d postgres

tests:
	docker-compose run --rm -e SYMFONY_DEPRECATIONS_HELPER=disabled api php vendor/bin/codecept run --fail-fast

clean:
	docker-compose down
	docker-compose rm
	rm -rf vendor/

down: 
	docker-compose down

start:
	docker-compose up -d api

run: config_common composer_install start

bash:
	docker-compose run --rm api sh
	
