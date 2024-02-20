<?php

declare(strict_types=1);

require("vendor/autoload.php");

$client = \Dagger\Dagger::connect();

$php = \Sai\PHPRuntime::create($client)
    ->version('8.3')
    ->fpm()
    ->withExtensions('pdo_mysql', 'gd')
    ->getContainer()
    ->stdout();