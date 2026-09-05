# HCON for PHP
Parse [HCON](https://four.htmx.org/docs/hcon-guide) (htmx Configuration Object Notation) in [PHP](https://www.php.net).

## Quick start
Install the latest version of **HCON for PHP** with [Composer](https://getcomposer.org) package manager:

```shell
composer require mc2it/hcon
```

For detailed instructions, see the [installation guide](Installation.md).

## Usage
This library provides the `hcon_decode()` function, which allows you to convert a [HCON-formatted string](https://four.htmx.org/docs/hcon-guide) to an associative array:

```php
use function Mc2it\Hcon\hcon_decode;

// Using a HCON-formatted string...
$hcon = "FirstName:Cédric LastName:Belin Company:MC2IT IsDeveloper";
$assocArray = hcon_decode($hcon);
echo var_export($assocArray, return: true), PHP_EOL;

// Using a JSON-formatted string...
$json = '{"FirstName": "Cédric", "LastName": "Belin", "Company": "MC2IT", "IsDeveloper": true}';
$assocArray = hcon_decode($json);
echo var_export($assocArray, return: true), PHP_EOL;
```

And that's it! 😊
