<?php

declare(strict_types=1);

namespace MDClub\Facade\Service;

use MDClub\Initializer\Facade;
use MDClub\Service\Image;

/**
 * ImageService Facade
 *
 * @method static array      getUrls(string $key, int $timestamp)
 * @method static string     upload(array $images)
 * @method static void       update(string $key, array $data)
 * @method static void       deleteMultiple(array $keys)
 * @method static void       delete(string $key)
 * @method static bool       has($id)
 * @method static void       hasOrFail($id)
 * @method static array      hasMultiple(array $ids)
 * @method static array|null get($id)
 * @method static array      getOrFail($id)
 * @method static array      getMultiple(array $ids)
 * @method static array      getList()
 */
class ImageService extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Image::class;
    }
}
