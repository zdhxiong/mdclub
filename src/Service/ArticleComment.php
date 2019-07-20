<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Traits\Commentable;
use Psr\Container\ContainerInterface;

/**
 * 文章评论
 */
class ArticleComment extends Abstracts
{
    use Commentable;

    /**
     * @var \MDClub\Model\Article
     */
    protected $model;

    /**
     * @inheritDoc
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->model = $this->articleModel;
    }
}
