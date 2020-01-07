<?php

declare(strict_types=1);

namespace MDClub\Middleware\Transformer;

/**
 * 举报转换器中间件
 */
class Report extends Abstracts
{
    /**
     * @inheritDoc
     */
    protected function getTransformer(): string
    {
        return \MDClub\Transformer\Report::class;
    }
}
