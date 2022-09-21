<p align="center">
    <img src="https://github.com/devsquad-cockpit/php/blob/develop/cockpit-logo.png?raw=true" alt="Cockpit" title="Cockpit" width="300"/>
</p>

<p align="center" style="margin-top: 6px; margin-bottom: 10px;">
    <a href="https://devsquad.com">
        <img src="https://github.com/devsquad-cockpit/php/blob/develop/devsquad-logo.png?raw=true" alt="DevSquad" title="DevSquad" width="150"/>
    </a>
</p>

Cockpit is a beautiful error tracking package that will help your software team to track and fix errors.

## Composer Installation

This package is compatible with **PHP 7.3 or greater**.

```bash
composer require devsquad-cockpit/php
```

## Configuration
So that the cockpit can send the errors to the application you need to define this environment configuration in your .env file

```env
COCKPIT_URL=http://cockpit.app/webhook
```

## Set logged user

Using the abiaxo function when reporting an error, the cockpit will send the data of the logged in user according to what was returned by Closure.

```php
Cockpit::setUser(function() {
    return ['name'=>'name', 'email'=>'user@email.com'...];
});
```