# PHP Task - User API

## Instructions

Create an API that runs on PHP 7+, uses PostgreSQL for persistence and supports JSON request payloads. The application should have user accounts that can log in to the system using the Multi-factor authentication (MFA) authentication method. Each account can be either an Administrator or a User.
You can use any libraries you deem appropriate. For this exercise, the main goal is to analyze your skills with pure PHP coding language. So, please, avoid using a Framework and focus on PHP language only.

## User Requirements
* Can complete the registration step ✅

* Can login ✅

* Can update their name ✅

## Administrator Requirements
* Can do everything a User can do ✅

* Can create a new account (Users and Administrators) ✅

* Can deactivate an account ✅

## Extra Credit
* Support for composer installation of the API ✅

* Postman request collection for the API ✅

* Documentation ✅

## Delivery

The project should be added to a public Git repository in any Git host and a URL to the repository must be sent before the end of the deadline. The project should include a README.md file outlining the steps to install and run the project. No external dependencies should be necessary to run the project!

# Install

To install the project you can clone the Docker repository (PHP 7.4 + NGINX + PostgreSQL) and install the API via composer

```
git clone https://github.com/felipeiise/xogito-docker.git
```
Move to the cloned repository:
```
cd xogito-docker
```
Start the Docker building process (this can take several minutes)
```
docker compose up -d --build
```
Enter to recently created PHP container:
```
docker exec -it php-srv bash
```
And install the API project with composer (take care to copy the `dot` at the end of the line below)
```
composer create-project felipeiise/xogito-api .
```
Rename the .env.example file to .env
```
mv .env.example .env
```
And that's it, you can just go to the browser and navigate to:

[http://localhost/](http://localhost/)

There's one initial administrator:

Email: first_admin@email.com

Password: 12345678



Or you can install the API not using Docker, just only via composer to some folder in your computer:
```
composer create-project felipeiise/xogito-api
```
```
cd xogito-api
```
Rename the .env.example file to .env
```
mv .env.example .env
```

And you will need to replace the ENVIRONMENT variables in your local `.env` file with your current local installed PostgreSQL credentials:
```
DB_HOST=localhost
DB_USER=postgres
DB_PASSWORD=docker
DB_DATABASE=postgres
DB_PORT=5432
```

There are 2 `.sql` files to execute in the database located in `sql` folder at the root of the repository:
```
create_tables.sql
initial_data.sql
```

To start the API in terminal enter:

```
php -S http://localhost:8080
```

And navigate to:
```
http://localhost:8080
```
There's also a folder called `postman` with a Collection and an Environment file to import and test the API routes.

Author: [@felipeiise](https://github.com/felipeiise)
