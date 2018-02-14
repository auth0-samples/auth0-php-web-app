# Auth0 + PHP Web App Sample

This sample demonstrates how to add authorization to a [PHP](http://php.net/) web app using [Auth0](https://auth0.com).

Check the [PHP Quickstart](https://auth0.com/docs/quickstart/webapp/php) to better understand this sample.

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

## What is Auth0?

Auth0 helps you to:

* Add authentication with [multiple authentication sources](https://docs.auth0.com/identityproviders), either social like **Google, Facebook, Microsoft Account, LinkedIn, GitHub, Twitter, Box, Salesforce, amont others**, or enterprise identity systems like **Windows Azure AD, Google Apps, Active Directory, ADFS or any SAML Identity Provider**.
* Add authentication through more traditional **[username/password databases](https://docs.auth0.com/mysql-connection-tutorial)**.
* Add support for **[linking different user accounts](https://docs.auth0.com/link-accounts)** with the same user.
* Support for generating signed [Json Web Tokens](https://docs.auth0.com/jwt) to call your APIs and **flow the user identity** securely.
* Analytics of how, when and where users are logging in.
* Pull data from other sources and add it to the user profile, through [JavaScript rules](https://docs.auth0.com/rules).

## Create a free account in Auth0

1. Go to [Auth0](https://auth0.com) and click Sign Up.
2. Use Google, GitHub or Microsoft Account to login.

## Issue Reporting

If you have found a bug or if you have a feature request, please report them at this repository issues section. Please do not report security vulnerabilities on the public GitHub issue tracker. The [Responsible Disclosure Program](https://auth0.com/whitehat) details the procedure for disclosing security issues.

## Author

[Auth0](https://auth0.com)

## License

This project is licensed under the MIT license. See the [LICENSE](LICENSE.txt) file for more info.
