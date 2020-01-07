<?php

declare(strict_types=1);

namespace MDClub\Middleware\Transformer;

/**
 * 回答转换器中间件
 */
class Answer extends Abstracts
{
    /**
     * @inheritDoc
     */
    protected function getTransformer(): string
    {
        return \MDClub\Transformer\Answer::class;
    }
}
