<?php

declare(strict_types=1);

namespace MDClub\Middleware\Transform;

use Psr\Container\ContainerInterface;

/**
 * 图片格式转换中间件
 *
 * @property-read \MDClub\Transformer\Image $imageTransformer
 */
class Image extends Abstracts
{
    /**
     * @var \MDClub\Transformer\Image
     */
    protected $transformer;

    /**
     * @inheritDoc
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->transformer = $this->imageTransformer;
    }
}
