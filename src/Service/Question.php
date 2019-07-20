<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Traits\Getable;
use Psr\Container\ContainerInterface;

/**
 * 提问
 */
class Question extends Abstracts
{
    use Getable;

    /**
     * @var \MDClub\Model\Question
     */
    protected $model;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->model = $this->questionModel;
    }

    /**
     * 根据 user_id 获取提问列表
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
     * 获取未删除的提问列表
     *
     * @return array
     */
    public function getList(): array
    {
        return $this->model->getList();
    }
}
