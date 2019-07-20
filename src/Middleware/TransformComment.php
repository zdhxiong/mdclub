<?php

declare(strict_types=1);

namespace MDClub\Middleware;

use Psr\Container\ContainerInterface;

/**
 * 评论格式转换中间件
 */
class TransformComment extends TransformAbstract
{
    /**
     * @var \MDClub\Transformer\Comment
     */
    protected $transformer;

    /**
     * @inheritDoc
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->transformer = $this->commentTransformer;
    }
}
