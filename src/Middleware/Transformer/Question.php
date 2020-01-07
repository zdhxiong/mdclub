<?php

declare(strict_types=1);

namespace MDClub\Middleware\Transformer;

/**
 * 提问转换器中间件
 */
class Question extends Abstracts
{
    /**
     * @inheritDoc
     */
    protected function getTransformer(): string
    {
        return \MDClub\Transformer\Question::class;
    }
}
