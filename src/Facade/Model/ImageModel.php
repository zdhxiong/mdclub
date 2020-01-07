<?php

declare(strict_types=1);

namespace MDClub\Facade\Model;

use MDClub\Model\Image;

/**
 * ImageModel Facade
 *
 * @method static array getList()
 */
class ImageModel extends Abstracts
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Image::class;
    }
}
