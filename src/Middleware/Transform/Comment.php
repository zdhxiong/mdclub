<?php

declare(strict_types=1);

namespace MDClub\Middleware\Transform;

use Psr\Container\ContainerInterface;

/**
 * 评论格式转换中间件
 *
 * @property-read \MDClub\Transformer\Comment $commentTransformer
 */
class Comment extends Abstracts
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
