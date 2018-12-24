<?php

declare(strict_types=1);

namespace App\Library;

use Monolog\Handler\StreamHandler;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * 日志
 *
 * Class Logger
 * @package App\Library
 */
class Logger implements LoggerInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Logger constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $logger = new \Monolog\Logger('mdclub');

        $logger->pushProcessor(new UidProcessor());
        $logger->pushHandler(new StreamHandler(
            __DIR__ . '/../../var/logs/' . date('Y-m') . '/' . date('d') . '.log',
            \Monolog\Logger::DEBUG
        ));

        $this->logger = $logger;
    }

    public function emergency($message, array $context = array())
    {
        return $this->logger->emergency($message, $context);
    }

    public function alert($message, array $context = array())
    {
        return $this->logger->alert($message, $context);
    }

    public function critical($message, array $context = array())
    {
        return $this->logger->critical($message, $context);
    }

    public function error($message, array $context = array())
    {
        return $this->logger->err($message, $context);
    }

    public function warning($message, array $context = array())
    {
        return $this->logger->warning($message, $context);
    }

    public function notice($message, array $context = array())
    {
        return $this->logger->notice($message, $context);
    }

    public function info($message, array $context = array())
    {
        return $this->logger->info($message, $context);
    }

    public function debug($message, array $context = array())
    {
        return $this->logger->debug($message, $context);
    }

    public function log($level, $message, array $context = array())
    {
        return $this->logger->log($level, $message, $context);
    }
}
