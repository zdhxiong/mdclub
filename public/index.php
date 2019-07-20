<?php

declare(strict_types=1);

use Slim\ResponseEmitter;
use MDClub\Initializer\App;

require __DIR__ . '/../vendor/autoload.php';

$app = new App();
$response = $app->run();

$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);
