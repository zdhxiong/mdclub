<?php

declare(strict_types=1);

namespace App\Abstracts;

use Psr\Container\ContainerInterface;
use App\Constant\ErrorConstant;
use App\Exception\ApiException;
use App\Exception\ValidationException;
use App\Helper\ValidatorHelper;
use App\Service\Service;

/**
 * 评论抽象类
 *
 * Class CommentAbstracts
 * @package App\Abstracts
 */
abstract class CommentableAbstracts extends Service
{
    /**
     * 评论类型（question、answer、article）
     *
     * @var string
     */
    protected $commentableType;

    /**
     * 评论目标的 Model 实例
     */
    protected $commentableTargetModel;

    /**
     * 评论目标的 Service 实例
     */
    protected $commentableTargetService;

    /**
     * CommentableAbstracts constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->commentableTargetModel = $this->{$this->commentableType . 'Model'};
        $this->commentableTargetService = $this->{$this->commentableType . 'Service'};
    }

    /**
     * 获取评论列表
     *
     * @param  int   $commentableId
     * @param  bool  $withRelationship
     * @return array
     */
    public function getComments(int $commentableId, bool $withRelationship = false): array
    {
        $this->commentableIdOrFail($commentableId);

        $list = $this->commentModel
            ->where([
                'commentable_id'   => $commentableId,
                'commentable_type' => $this->commentableType,
            ])
            ->order($this->commentService->getOrder(['create_time' => 'ASC']))
            ->field($this->commentService->getPrivacyFields(), true)
            ->paginate();

        if ($withRelationship) {
            $list['data'] = $this->commentService->addRelationship($list['data']);
        }

        return $list;
    }

    /**
     * 添加评论
     *
     * @param  int    $commentableId
     * @param  string $content
     * @return int                   评论ID
     */
    public function addComment(int $commentableId, string $content = null): int
    {
        $userId = $this->roleService->userIdOrFail();
        $this->commentableIdOrFail($commentableId);

        if ($content) {
            $content = strip_tags($content);
        }

        // 评论最多 1000 个字，最少 1 个字
        $errors = [];
        if (!$content) {
            $errors['content'] = '评论内容不能为空';
        } elseif (!ValidatorHelper::isMax($content, 1000)) {
            $errors['content'] = '评论内容不能超过 1000 个字符';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }

        return (int)$this->commentModel->insert([
            'commentable_id'   => $commentableId,
            'commentable_type' => $this->commentableType,
            'user_id'          => $userId,
            'content'          => $content,
        ]);
    }

    /**
     * 若评论目标不存在，则抛出异常
     *
     * @param int $commentableId
     */
    protected function commentableIdOrFail(int $commentableId): void
    {
        if (!$this->commentableTargetService->has($commentableId)) {
            $this->throwCommentableNotFoundException();
        }
    }

    /**
     * 抛出评论目标不存在的异常
     *
     * @throws ApiException
     */
    protected function throwCommentableNotFoundException(): void
    {
        $constants = [
            'article'  => ErrorConstant::ARTICLE_NOT_FOUND,
            'question' => ErrorConstant::QUESTION_NOT_FOUND,
            'answer'   => ErrorConstant::ANSWER_NOT_FOUNT,
        ];

        throw new ApiException($constants[$this->commentableType]);
    }
}
