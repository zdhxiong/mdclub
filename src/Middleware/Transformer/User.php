<?php

declare(strict_types=1);

namespace MDClub\Middleware\Transformer;

/**
 * 用户转换器中间件
 */
class User extends Abstracts
{
    /**
     * @inheritDoc
     */
    protected function getTransformer(): string
    {
        return \MDClub\Transformer\User::class;
    }
}
