<?php

declare(strict_types=1);

namespace MDClub\Middleware\Transform;

use Psr\Container\ContainerInterface;

/**
 * 提问格式转换中间件
 *
 * @property-read \MDClub\Transformer\Question $questionTransformer
 */
class Question extends Abstracts
{
    /**
     * @var \MDClub\Transformer\Question
     */
    protected $transformer;

    /**
     * @inheritDoc
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->transformer = $this->questionTransformer;
    }
}
