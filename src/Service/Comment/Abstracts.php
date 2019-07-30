<?php

declare(strict_types=1);

namespace MDClub\Service\Comment;

use MDClub\Service\Abstracts as ServiceAbstracts;
use Psr\Container\ContainerInterface;

/**
 * 评论抽象类
 */
abstract class Abstracts extends ServiceAbstracts
{
    /**
     * @var \MDClub\Model\Comment
     */
    protected $model;

    /**
     * @inheritDoc
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->model = $this->commentModel;
    }
}
