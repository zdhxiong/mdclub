<?php

declare(strict_types=1);

namespace App\Library;

use Monolog\Handler\StreamHandler;
use Monolog\Processor\UidProcessor;

/**
 * 日志
 */
class Logger extends \Monolog\Logger
{
    public function __construct()
    {
        $name = 'mdclub';
        $processors[] = new UidProcessor();
        $handlers[] = new StreamHandler(
            __DIR__ . '/../../var/logs/' . date('Y-m') . '/' . date('d') . '.log',
            \Monolog\Logger::DEBUG
        );

        parent::__construct($name, $handlers, $processors);
    }
}
