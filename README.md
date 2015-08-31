#Create packages

This package is inspired by the workbench package that came default in Laravel 4.
It speeds up your workflow for creating packages, once you have set your config settings the only thing left
is running the artisan command and start developing your package.

##Installation

You can install this package through composer by running the following command

```php
    $ composer require jorenvanhocht\create-packages 1.0
```

Now add the service provider to the provider array in ```config/app.php```

```php
    jorenvanhocht\CreatePackages\Providers\CreatePackagesServiceProvider::class,
```

##Configuration

Publish the config file by running the following command from your terminal

```php
    $ php artisan vendor:publish
```

Set your base folder and your vendor name, and you are good to go.

##Usage

To create a new package run

```php
    $ php artisan make:package yourPackageName
```

If want to create a package with a different vendor name then set in your config file you can add it as a parameter

```php
    $ php artisan make:package yourPackageName YourNewVendorName
```