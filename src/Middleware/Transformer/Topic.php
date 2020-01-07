<?php

declare(strict_types=1);

namespace MDClub\Middleware\Transformer;

/**
 * 话题转换器中间件
 */
class Topic extends Abstracts
{
    /**
     * @inheritDoc
     */
    protected function getTransformer(): string
    {
        return \MDClub\Transformer\Topic::class;
    }
}
