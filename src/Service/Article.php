<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Traits\Commentable;
use MDClub\Traits\Deletable;
use MDClub\Traits\Followable;
use MDClub\Traits\Getable;
use MDClub\Traits\Votable;
use Psr\Container\ContainerInterface;

/**
 * 文章
 */
class Article extends Abstracts
{
    use Getable;

    /**
     * @var \MDClub\Model\Article
     */
    protected $model;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->model = $this->articleModel;
    }

    /**
     * 根据 user_id 获取文章列表
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
     * 获取未删除的文章列表
     *
     * @return array
     */
    public function getList(): array
    {
        return $this->model->getList();
    }
}
