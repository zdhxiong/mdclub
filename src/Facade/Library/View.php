<?php

declare(strict_types=1);

namespace MDClub\Facade\Library;

use MDClub\Initializer\Facade;
use Psr\Http\Message\ResponseInterface;

/**
 * View Facade
 *
 * @method static ResponseInterface render($template, array $data = [])
 * @method static string            fetch(string $template, array $data = [])
 */
class View extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return \MDClub\Library\View::class;
    }
}
