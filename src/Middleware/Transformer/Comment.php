<?php

declare(strict_types=1);

namespace MDClub\Middleware\Transformer;

/**
 * 评论转换器中间件
 */
class Comment extends Abstracts
{
    /**
     * @inheritDoc
     */
    protected function getTransformer(): string
    {
        return \MDClub\Transformer\Comment::class;
    }
}
