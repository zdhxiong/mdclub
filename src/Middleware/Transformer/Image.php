<?php

declare(strict_types=1);

namespace MDClub\Middleware\Transformer;

/**
 * 图片转换器中间件
 */
class Image extends Abstracts
{
    /**
     * @inheritDoc
     */
    protected function getTransformer(): string
    {
        return \MDClub\Transformer\Image::class;
    }
}
