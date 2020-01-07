<?php

declare(strict_types=1);

namespace MDClub\Facade\Validator;

use MDClub\Initializer\Facade;
use MDClub\Validator\Image;
use Psr\Http\Message\UploadedFileInterface;

/**
 * ImageValidator Facade
 *
 * @method static UploadedFileInterface upload(array $images)
 * @method static array                 update(string $key, array $data)
 */
class ImageValidator extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Image::class;
    }
}
