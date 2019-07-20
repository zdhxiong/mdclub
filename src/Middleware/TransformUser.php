<?php

declare(strict_types=1);

namespace MDClub\Middleware;

use Psr\Container\ContainerInterface;

/**
 * 用户格式转换中间件
 */
class TransformUser extends TransformAbstract
{
    /**
     * @var \MDClub\Transformer\User
     */
    protected $transformer;

    /**
     * @inheritDoc
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->transformer = $this->userTransformer;
    }
}
