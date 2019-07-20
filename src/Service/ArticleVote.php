<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Traits\Votable;
use Psr\Container\ContainerInterface;

/**
 * 文章投票
 */
class ArticleVote extends Abstracts
{
    use Votable;

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
