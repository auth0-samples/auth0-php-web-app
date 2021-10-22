# Auth0 PHP Web App Sample

This sample demonstrates how to add authorization to a [PHP](http://php.net/) web app using [Auth0](https://auth0.com).

Check the [PHP Quickstart](https://auth0.com/docs/quickstart/webapp/php) to understand this sample better.

## Configuration

### Create a free account in Auth0

1. Go to [Auth0](https://auth0.com) and click Sign Up.
2. Use Google, GitHub or Microsoft Account to login.

### Create an Auth0 Application

You will need to create a Regular Web Application using the [Auth0 Dashboard](https://manage.auth0.com). This will give you a Domain, Client ID, and Client Secret you will need below.

### Configure Credentials

Your project needs to be configured with your Auth0 Domain, Client ID, and Client Secret for the authentication flow to work.

Copy .env.example into a new file in the same folder called .env, and replace the values with your Auth0 application credentials:

```sh
# Your Auth0 application's Client ID
AUTH0_CLIENT_ID='YOUR_AUTH0_CLIENT_ID'

# The url of your Auth0 tenant domain
AUTH0_DOMAIN='https://YOUR_AUTH0_DOMAIN.auth0.com'

# Your Auth0 application's Client Secret
AUTH0_CLIENT_SECRET='YOUR_AUTH0_CLIENT_SECRET'

# A long secret value used to encrypt the session cookie
AUTH0_COOKIE_SECRET='LONG_RANDOM_VALUE'
```

**Note**: Make sure you replace `LONG_RANDOM_VALUE` with your secret (you can generate a suitable string using `openssl rand -hex 32` on the command line).

**Note**: Ensure you are consistent in your use of 'localhost' and/or '127.0.0.1' when testing. These must match your Auth0 Application settings or you will encounter errors. They must also match for session cookies to work correctly.

## Run the sample

Before continuing, please ensure you have [PHP](https://www.php.net/manual/en/install.php) 7.4+ and [Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos) installed and accessible from your shell. These are required.

Next, use the following command to install the necessary dependencies and start the sample:

```bash
composer run app
```

Your Quickstart should now be accessible at [http://127.0.0.1:3000/](http://127.0.0.1:3000/) from your web browser.

## Running with Docker

Before continuing, make sure you have [Docker](https://docs.docker.com/get-docker/) installed. This is required.

Next, use the following command to install the necessary dependencies and start the sample within a Docker container:

```bash
composer run docker
```

Your Quickstart should now be accessible at [http://127.0.0.1:3000/](http://127.0.0.1:3000/) from your web browser.

## Running the unit tests

Unit tests are setup to run through [Docker](https://docs.docker.com/get-docker/) for portability. Use the following command to install the necessary dependencies and start the sample test suite:

```bash
composer run tests
```

## Vulnerability Reporting

Please do not report security vulnerabilities on the public GitHub issue tracker. The [Responsible Disclosure Program](https://auth0.com/whitehat) details the procedure for disclosing security issues.

## What is Auth0?

Auth0 helps you to easily:

- implement authentication with multiple identity providers, including social (e.g., Google, Facebook, Microsoft, LinkedIn, GitHub, Twitter, etc), or enterprise (e.g., Windows Azure AD, Google Apps, Active Directory, ADFS, SAML, etc.)
- log in users with username/password databases, passwordless, or multi-factor authentication
- link multiple user accounts together
- generate signed JSON Web Tokens to authorize your API calls and flow the user identity securely
- access demographics and analytics detailing how, when, and where users are logging in
- enrich user profiles from other data sources using customizable JavaScript rules

[Why Auth0?](https://auth0.com/why-auth0)

## License

This project is licensed under the MIT license. See the [LICENSE](https://github.com/auth0-samples/auth0-php-web-app/blob/master/LICENSE) file for more info.
