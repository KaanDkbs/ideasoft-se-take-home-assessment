# Installation

#### This project is built using Laradock, Laravel, and Docker.

#

First, go to a directory to clone the project files and run the following command:
```shell
git clone https://github.com/KaanDkbs/ideasoft-se-take-home-assessment.git
```

Then, go to the Laradock folder:
```shell
cd laradock
```

To start the Docker containers, run the following command:
```shell
docker-compose up -d nginx mysql phpmyadmin workspace
```
Create Database
```shell
"laravel_app" named database must be created. You can access PhpMyAdmin at http://localhost:8081
```

To connect to the workspace container, run the following command:
```shell
docker-compose exec workspace bash
```

To install the required PHP libraries, run the following command in the workspace container:
```shell
composer install
```

To create the database tables, run the following command:
```shell
php artisan migrate --seed
```

To create encryption keys for Passport, run the following command:
```shell
php artisan passport:install
```

## Usage
The project runs on http://localhost

