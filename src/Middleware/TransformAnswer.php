<?php

declare(strict_types=1);

namespace MDClub\Middleware;

use Psr\Container\ContainerInterface;

/**
 * 回答格式转换中间件
 */
class TransformAnswer extends TransformAbstract
{
    /**
     * @var \MDClub\Transformer\Answer
     */
    protected $transformer;

    /**
     * @inheritDoc
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->transformer = $this->answerTransformer;
    }
}
