<?php

declare(strict_types=1);

namespace MDClub\Facade\Library;

use MDClub\Initializer\Facade;
use Psr\Http\Message\StreamInterface;

/**
 * Storage Facade
 *
 * @method static array get(string $path, array $thumbs)
 * @method static void  write(string $path, StreamInterface $stream, array $thumbs)
 * @method static void delete(string $path, array $thumbs)
 */
class Storage extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return \MDClub\Library\Storage::class;
    }
}
