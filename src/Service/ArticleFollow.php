<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Traits\Followable;
use Psr\Container\ContainerInterface;

/**
 * 文章关注
 */
class ArticleFollow extends Abstracts
{
    use Followable;

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
