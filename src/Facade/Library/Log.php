<?php

declare(strict_types=1);

namespace MDClub\Facade\Library;

use MDClub\Initializer\Facade;

/**
 * Log Facade
 *
 * @method static void emergency($message, array $context = array())
 * @method static void alert($message, array $context = array())
 * @method static void critical($message, array $context = array())
 * @method static void error($message, array $context = array())
 * @method static void warning($message, array $context = array())
 * @method static void notice($message, array $context = array())
 * @method static void info($message, array $context = array())
 * @method static void debug($message, array $context = array())
 * @method static void log($level, $message, array $context = array())
 */
class Log extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return \MDClub\Library\Log::class;
    }
}
