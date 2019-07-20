<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Traits\Commentable;
use Psr\Container\ContainerInterface;

/**
 * 回答评论
 */
class AnswerComment extends Abstracts
{
    use Commentable;

    /**
     * @var \MDClub\Model\Answer
     */
    protected $model;

    /**
     * @inheritDoc
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->model = $this->answerModel;
    }
}
