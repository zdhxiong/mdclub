<?php

declare(strict_types=1);

namespace App\Library;

use App\Interfaces\ContainerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\UidProcessor;

/**
 * 日志
 *
 * Class Logger
 * @package App\Library
 */
class Logger extends \Monolog\Logger
{
    /**
     * Logger constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct($container)
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
