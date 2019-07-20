<?php

declare(strict_types=1);

namespace MDClub\Observer;

/**
 * 评论观察者
 */
class Comment extends Abstracts
{
    /**
     * 创建评论后
     *
     * @param array $comment
     */
    public function created(array $comment): void
    {
    }
}
