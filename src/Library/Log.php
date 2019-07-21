<?php

declare(strict_types=1);

namespace MDClub\Library;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;

/**
 * 实现了 PSR3 接口的日志
 */
class Log extends MonologLogger
{
    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $runtime = $container->get('settings')['runtime'];
        $name = 'mdclub';
        $processors[] = new UidProcessor();
        $handlers[] = new StreamHandler(
            $runtime . '/logs/' . date('Y-m') . '/' . date('d') . '.log',
            MonologLogger::DEBUG
        );

        parent::__construct($name, $handlers, $processors);
    }
}
