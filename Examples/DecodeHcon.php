<?php declare(strict_types=1);
use function Mc2it\Hcon\hcon_decode;

// Using a HCON-formatted string...
$hcon = "FirstName:Cédric LastName:Belin Company:MC2IT IsDeveloper";
$assocArray = hcon_decode($hcon);
echo var_export($assocArray, return: true), PHP_EOL;

// Using a JSON-formatted string...
$json = '{"FirstName": "Cédric", "LastName": "Belin", "Company": "MC2IT", "IsDeveloper": true}';
$assocArray = hcon_decode($json);
echo var_export($assocArray, return: true), PHP_EOL;
