<?php

declare(strict_types=1);

namespace MDClub\Service\Comment;

use MDClub\Abstracts\ContainerProperty;
use Psr\Container\ContainerInterface;

/**
 * 评论抽象类
 */
abstract class Abstracts extends ContainerProperty
{
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
}
