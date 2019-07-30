<?php

declare(strict_types=1);

namespace MDClub\Middleware\Transform;

use Psr\Container\ContainerInterface;

/**
 * 举报原因转换器
 *
 * @property-read \MDClub\Transformer\ReportReason $reportReasonTransformer
 */
class ReportReason extends Abstracts
{
    /**
     * @var \MDClub\Transformer\ReportReason
     */
    protected $transformer;

    /**
     * @inheritDoc
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->transformer = $this->reportReasonTransformer;
    }
}
