<?php

declare(strict_types=1);

namespace MDClub\Middleware;

use Psr\Container\ContainerInterface;

/**
 * 文章格式转换中间件
 */
class TransformArticle extends TransformAbstract
{
    /**
     * @var \MDClub\Transformer\Article
     */
    protected $transformer;

    /**
     * @inheritDoc
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->transformer = $this->articleTransformer;
    }
}
