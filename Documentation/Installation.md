# Installation

## Requirements
Before installing **HCON for PHP**, you need to make sure you have [PHP](https://www.php.net)
and [Composer](https://getcomposer.org), the PHP package manager, up and running.
	
You can verify if you're already good to go with the following commands:

```shell
php --version
# PHP 8.5.10 (cli) (built: Aug 25 2026 21:23:48) (NTS Visual C++ 2022 x64)

composer --version
# Composer version 2.10.3 2026-08-27 13:34:23
```

## Installing with Composer package manager

### 1. Install it
From a command prompt, run:

```shell
composer require mc2it/hcon
```

### 2. Import it
Now in your [PHP](https://www.php.net) code, you can use:

```php
use function Mc2it\Hcon\hcon_decode;
```
