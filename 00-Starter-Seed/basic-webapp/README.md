# Auth0 + PHP Web App Sample

## Getting Started

Before starting, make sure you have `composer` and `php` installed.

Rename the `.env.example` file to `.env` and populate it with your app's client ID, client secret, domain, and callback URL. These can be retrieved from your [Auth0 dashboard](https://manage.auth0.com). 

## Running the App

```bash
composer install
php -S localhost:3000
```

The app will be served at [http://localhost:3000/](http://localhost:3000/).

## Running the App With Docker

Before starting, make sure you have `docker` installed.

Rename the `.env.example` file to `.env` and populate it like explained [previously](#getting-started).

Execute in command line `sh exec.sh` to run the Docker in Linux, or `.\exec.ps1` to run the Docker in Windows.

The app will be served at [http://localhost:3000/](http://localhost:3000/).