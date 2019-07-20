<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Traits\Votable;
use Psr\Container\ContainerInterface;

/**
 * 评论投票
 */
class CommentVote extends Abstracts
{
    use Votable;

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
