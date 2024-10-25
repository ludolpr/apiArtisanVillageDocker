setup:
	@make build
	@make up
	@make composer-update

up:
	docker-compose up -d

down:
	docker-compose down

build:
	docker-compose build
	
buildup:
	docker-compose up --build
stop:
	docker-compose stop	
composer-update:
	docker exec laravel-docker bash -c "composer update"
data:
	docker exec laravel-docker bash -c "php artisan migrate"
	docker exec laravel-docker bash -c "php artisan db:seed"
