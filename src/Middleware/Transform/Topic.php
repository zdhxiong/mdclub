<?php

declare(strict_types=1);

namespace MDClub\Middleware\Transform;

use Psr\Container\ContainerInterface;

/**
 * 话题格式转换中间件
 *
 * @property-read \MDClub\Transformer\Topic $topicTransformer
 */
class Topic extends Abstracts
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
