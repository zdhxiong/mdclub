<?php

declare(strict_types=1);

namespace MDClub\Middleware;

use Psr\Container\ContainerInterface;

/**
 * 图片格式转换中间件
 */
class TransformImage extends TransformAbstract
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
