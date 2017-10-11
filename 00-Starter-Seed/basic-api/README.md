# Auth0 + PHP API Seed
This is the seed project you need to use if you're going to create a PHP API. You'll mostly use this API either for a SPA or a Mobile app. If you just want to create a Regular PHP WebApp, please check [this other seed project](https://github.com/auth0/auth0-PHP/tree/master/examples/basic-webapp)

## Running the example
In order to run the example you need to have `composer` and `php` installed.

You also need to set the ClientSecret and ClientId for your Auth0 app as enviroment variables with the following names respectively: `AUTH0_CLIENT_SECRET` and `AUTH0_CLIENT_ID`.

For that, if you just create a file named `.env` in the directory and set the values like the following, the app will just work:

````bash
# .env file
AUTH0_CLIENT_SECRET=myCoolSecret
AUTH0_CLIENT_ID=myCoolClientId
````

Once you've set those 2 enviroment variables, just run the following to get the app started:

````bash
composer install
php -S localhost:3001
````

The app will be served at [http://localhost:3001/](http://localhost:3001/).

## Running the App With Docker

Before starting, make sure you have `docker` installed.

Rename the `.env.example` file to `.env` and populate it like explained [previously](#running-the-example).

Execute in command line `sh exec.sh` to run the Docker in Linux, or `.\exec.ps1` to run the Docker in Windows.

The app will be served at [http://localhost:3001/](http://localhost:3001/).