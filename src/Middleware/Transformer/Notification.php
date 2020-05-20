<?php

declare(strict_types=1);

namespace MDClub\Middleware\Transformer;

/**
 * 通知转换器中间件
 */
class Notification extends Abstracts
{
    /**
     * @inheritDoc
     */
    protected function getTransformer(): string
    {
        return \MDClub\Transformer\Notification::class;
    }
}
