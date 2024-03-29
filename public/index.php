<?php

use App\Kernel;

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

ini_set('max_execution_time', '300'); # TODO paramétrer ici ou juste lors d'une compilation latex ?
ini_set("memory_limit", "1G");

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool)$context['APP_DEBUG']);
};
