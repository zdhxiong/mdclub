<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Traits\Getable;
use Psr\Container\ContainerInterface;

/**
 * 评论
 */
class Comment extends Abstracts
{
    use Getable;

    /**
     * @var \MDClub\Model\Comment
     */
    protected $model;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->model = $this->commentModel;
    }

    /**
     * 根据 user_id 获取评论列表
     *
     * @param  int   $userId
     * @return array
     */
    public function getByUserId(int $userId): array
    {
        $this->userService->hasOrFail($userId);

        return $this->model->getByUserId($userId);
    }

    /**
     * 获取未删除的评论列表
     *
     * @return array
     */
    public function getList(): array
    {
        return $this->model->getList();
    }
}
