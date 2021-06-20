# Getting started with Limbo/Container
[![Latest Stable Version](https://poser.pugx.org/limbo/container/v/stable)](https://packagist.org/packages/limbo/container)
[![Latest Unstable Version](https://poser.pugx.org/limbo/container/v/unstable)](https://packagist.org/packages/limbo/container)
[![License](https://poser.pugx.org/limbo/container/license)](https://packagist.org/packages/limbo/container)
[![Downloads](https://poser.pugx.org/limbo/container/downloads)](https://packagist.org/packages/limbo/container)

[psr/container](https://www.php-fig.org/psr/psr-11/) implementation for humans

## Features

 * Support Autowiring (Based on reflection for class and Closure).
 * Autowiring support variadic arguments.
 * Can contain any values.

## Install via [composer](https://getcomposer.org/)

```
composer require limbo/container
```

### Create container

```php
require_once 'vendor/autoload.php';

use \Limbo\Container\Container;

$container = new Container();
```

or 

```php
require_once 'vendor/autoload.php';

use PDO;
use Limbo\Container\Container;
use Limbo\Container\Definition\Definition;

$definitions = [
    new Definition('database', [
        'hostname' => '127.0.0.1',
        'dbname' => 'app_db',
        'username' => 'root',
        'password' => 'superpass',
    ]),
    new Definition(PDO::class, function(array $database) {
        $dsn = "mysql:host={$database['hostname']};dbname={$database['dbname']}";
        
        return new PDO(
            $dsn, 
            $database['username'],
            $database['password'],
            []
        );
    })
];

$container = new Container($definitions);
```
