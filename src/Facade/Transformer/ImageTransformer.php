<?php

declare(strict_types=1);

namespace MDClub\Facade\Transformer;

use MDClub\Initializer\Facade;
use MDClub\Transformer\Image;

/**
 * ImageTransformer Facade
 *
 * @method static void  setInclude(array $includes)
 * @method static array transform(array $items, array $knownRelationship = [])
 * @method static array getInRelationship(array $keys)
 */
class ImageTransformer extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Image::class;
    }
}
