<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Traits\Followable;
use MDClub\Traits\Getable;
use Psr\Container\ContainerInterface;

/**
 * 话题
 */
class Topic extends Abstracts
{
    use Getable;

    /**
     * @var \MDClub\Model\Topic
     */
    protected $model;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->model = $this->topicModel;
    }

    /**
     * 获取话题列表
     *
     * @return array
     */
    public function getList(): array
    {
        return $this->topicModel->getList();
    }

    /**
     * 获取已删除的话题列表
     *
     * @return array
     */
    public function getDeleted(): array
    {
        return $this->topicModel->getDeleted();
    }
}
