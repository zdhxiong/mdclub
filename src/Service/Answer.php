<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Traits\Getable;
use Psr\Container\ContainerInterface;

/**
 * 回答
 *
 * @property-read \MDClub\Model\Answer $answerModel
 * @property-read Question             $questionService
 * @property-read User                 $userService
 */
class Answer extends Abstracts
{
    use Getable;

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

    /**
     * 根据 user_id 获取回答列表
     *
     * @param  int   $userId
     * @return array
     */
    public function getByUserId(int $userId): array
    {
        $this->userService->hasOrFail($userId);

        return $this->model->getByUserId($userId);
    }

    /**
     * 根据 question_id 获取回答列表
     *
     * @param  int   $questionId
     * @return array
     */
    public function getByQuestionId(int $questionId): array
    {
        $this->questionService->hasOrFail($questionId);

        return $this->model->getByQuestionId($questionId);
    }

    /**
     * 获取已删除的回答列表
     *
     * @return array
     */
    public function getDeleted(): array
    {
        return $this->model->getDeleted();
    }

    /**
     * 获取未删除的回答列表
     *
     * @return array
     */
    public function getList(): array
    {
        return $this->model->getList();
    }
}
