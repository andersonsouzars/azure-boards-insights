<?php

use App\Utils\EnvLoader;

require_once __DIR__ . '/vendor/autoload.php';

$envLoader = new EnvLoader(__DIR__ . '/.env');
$envLoader->load();

$appName = EnvLoader::get('TESTE', '456');
var_dump($appName);