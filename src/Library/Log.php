<?php

declare(strict_types=1);

namespace MDClub\Library;

use MDClub\Initializer\App;
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
        $runtime = App::$config['APP_RUNTIME'];
        $name = 'mdclub';
        $processors[] = new UidProcessor();
        $handlers[] = new StreamHandler(
            $runtime . '/logs/' . date('Y-m') . '/' . date('d') . '.log',
            MonologLogger::DEBUG
        );

        parent::__construct($name, $handlers, $processors);
    }
}
