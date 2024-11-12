SHELL=/bin/bash

UID := 1000

get-env:
	cp .env.example .env

up:
	env UID=${UID} docker compose up -d --build --remove-orphans

install:
	docker run --rm -it -v ${PWD}:/app -u ${UID}:${UID} composer install --no-scripts --no-interaction --optimize-autoloader  --ignore-platform-reqs
	env UID=${UID} docker compose exec -u app service-php php artisan package:discover
	env UID=${UID} docker compose restart  service-php

update:
	docker run --rm -it -v ${PWD}:/app -u ${UID}:${UID} composer update --no-scripts --no-interaction --optimize-autoloader --ignore-platform-reqs
	env UID=${UID} docker compose exec -u app service-php php artisan package:discover
	env UID=${UID} docker compose restart service-php

dump-autoload:
	docker run --rm -it -v ${PWD}:/app -u ${UID}:${UID} composer dump-autoload

migrate:
	env UID=${UID} docker compose exec -u app service-php php artisan migrate

migrate-refresh:
	env UID=${UID} docker compose exec -u app service-php php artisan migrate:refresh

run-seed:
	env UID=${UID} docker compose exec -u app service-php php artisan db:seed

test:
	env UID=${UID} docker compose exec -u app service-php php artisan test

user-management-role-init:
	env UID=${UID} docker compose exec -u app service-php php artisan vendor:publish --provider="UserManagementModule\Providers\UserManagementModuleProvider" 
	env UID=${UID} docker compose exec -u app service-php php artisan migrate --path=./vendor/nikitinuser/user-management-module/UserManagementModule/database/migrations
	env UID=${UID} docker compose exec -u app service-php php artisan db:seed --class="UserManagementModule\\database\\seeders\\RolesSeed"

jwt-init:
	env UID=${UID} docker compose exec -u app service-php php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
	env UID=${UID} docker compose exec -u app service-php php artisan jwt:secret