<?php

declare(strict_types=1);

namespace App\Service\Comment;

use App\Abstracts\ContainerAbstracts;
use Psr\Container\ContainerInterface;

/**
 * 评论抽象类
 */
abstract class Abstracts extends ContainerAbstracts
{
    /**
     * @var \App\Model\Comment
     */
    protected $model;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->model = $this->commentModel;
    }
}
