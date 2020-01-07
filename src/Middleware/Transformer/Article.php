<?php

declare(strict_types=1);

namespace MDClub\Middleware\Transformer;

/**
 * 文章转换器中间件
 */
class Article extends Abstracts
{
    /**
     * @inheritDoc
     */
    protected function getTransformer(): string
    {
        return \MDClub\Transformer\Article::class;
    }
}
