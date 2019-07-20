<?php

declare(strict_types=1);

namespace MDClub\Middleware;

use Psr\Container\ContainerInterface;

/**
 * 话题格式转换中间件
 */
class TransformTopic extends TransformAbstract
{
    /**
     * @var \MDClub\Transformer\Topic
     */
    protected $transformer;

    /**
     * @inheritDoc
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->transformer = $this->topicTransformer;
    }
}
