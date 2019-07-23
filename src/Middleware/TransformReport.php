<?php

declare(strict_types=1);

namespace MDClub\Middleware;

use Psr\Container\ContainerInterface;

/**
 * 举报转换器
 */
class TransformReport extends TransformAbstract
{
    /**
     * @var \MDClub\Transformer\Report
     */
    protected $transformer;

    /**
     * @inheritDoc
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->transformer = $this->reportTransformer;
    }
}
