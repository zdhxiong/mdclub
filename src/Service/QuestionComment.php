<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Traits\Commentable;
use Psr\Container\ContainerInterface;

/**
 * 提问评论
 */
class QuestionComment extends Abstracts
{
    use Commentable;

    /**
     * @var \MDClub\Model\Question
     */
    protected $model;

    /**
     * @inheritDoc
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->model = $this->questionModel;
    }
}
