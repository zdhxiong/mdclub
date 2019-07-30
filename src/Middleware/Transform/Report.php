<?php

declare(strict_types=1);

namespace MDClub\Middleware\Transform;

use Psr\Container\ContainerInterface;

/**
 * 举报转换器
 *
 * @property-read \MDClub\Transformer\Report $reportTransformer
 */
class Report extends Abstracts
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
