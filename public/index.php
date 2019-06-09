<?php

declare(strict_types=1);

use Slim\App;

require __DIR__ . '/../vendor/autoload.php';

$settings = require __DIR__ . '/../config/settings.php';
$app = new App($settings);

require __DIR__ . '/../config/dependencies.php';
require __DIR__ . '/../config/middleware.php';
require __DIR__ . '/../config/routes.php';

$app->run();
