<?php

declare(strict_types=1);

namespace MDClub\Middleware\Transformer;

/**
 * 举报原因转换器中间件
 */
class ReportReason extends Abstracts
{
    /**
     * @inheritDoc
     */
    protected function getTransformer(): string
    {
        return \MDClub\Transformer\ReportReason::class;
    }
}
