# veezee-server-example
An example implementation of veezee HTTP API + management panel.
This server app can be used to communicate with the veezee app (iOS/Android).


## Warning
Do not use in a production environment without reviewing the security of the code.

## Features
- Written in PHP 7.1
- **MongoDB** as database
- Based on a custom made MVC framework (Soda)
- Simple user management + **Google accounts integration**
- Simple admin panel made for demonstration purposes

## How to use

Install dependencies using Composer with command `composer install`. Now You'll need a server that supports `.htaccess` such as Apache 2.4. You can then create a virtual host and set its root directory to `/wwwroot` of this project.

Then go to `wwwroot/` and rename the `example-content` folder to `content`. Also in `wwwroot` rename the `.example-htaccess` to `.htaccess` and then open it and change line 31 to match your setup (local address in which you configured Apache to run veezee server).

Then go to  `/config` and rename `example-app-config.php` to `app.config.php` and if you want to use Google sign in, delete `example-client_secret.json` and replace it with the `client_secret.json` file provided by Google.

Finally go to `/app` and rename `example-cache` folder to `cache`.
