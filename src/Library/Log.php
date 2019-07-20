<?php

declare(strict_types=1);

namespace MDClub\Library;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\UidProcessor;

/**
 * 实现了 PSR3 接口的日志
 */
class Log extends MonologLogger
{
    public function __construct()
    {
        $name = 'mdclub';
        $processors[] = new UidProcessor();
        $handlers[] = new StreamHandler(
            __DIR__ . '/../../var/logs/' . date('Y-m') . '/' . date('d') . '.log',
            MonologLogger::DEBUG
        );

        parent::__construct($name, $handlers, $processors);
    }
}
