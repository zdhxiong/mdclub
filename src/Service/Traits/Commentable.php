<?php

declare(strict_types=1);

namespace MDClub\Service\Traits;

use MDClub\Facade\Library\Auth;
use MDClub\Facade\Model\CommentModel;
use MDClub\Facade\Service\NotificationService;
use MDClub\Facade\Service\QuestionService;
use MDClub\Facade\Validator\CommentValidator;
use MDClub\Model\Abstracts as ModelAbstracts;

/**
 * 可评论对象。用于 question, answer, article
 */
trait Commentable
{
    /**
     * @inheritDoc
     */
    abstract public function getModelInstance(): ModelAbstracts;

    /**
     * @inheritDoc
     */
    public function getComments(int $commentableId): array
    {
        $this->hasOrFail($commentableId);

        $model = $this->getModelInstance();

        return CommentModel::getByCommentableId($model->table, $commentableId);
    }

    /**
     * @inheritDoc
     */
    public function addComment(int $commentableId, array $data): int
    {
        $model = $this->getModelInstance();
        $table = $model->table;

        // 评论目标, question | article | answer | comment
        /** @var QuestionService $class */
        $class = '\MDClub\Facade\Service\\' . ucfirst($table) . 'Service';
        $commentableTarget = $class::getOrFail($commentableId);

        $createData = CommentValidator::create($data);

        // 新增评论
        $commentId = (int)CommentModel
            ::set('commentable_type', $table)
            ->set('commentable_id', $commentableId)
            ->set('user_id', Auth::userId())
            ->set('content', $createData['content'])
            ->insert();

        // 发送通知
        if ($table !== 'comment') {
            NotificationService::add($commentableTarget['user_id'], "${table}_commented", [
                "${table}_id" => $commentableId,
                'comment_id' => $commentId,
            ])->send();
        } else {
            NotificationService::add($commentableTarget['user_id'], "${table}_replied", [
                'comment_id' => $commentableId,
                'reply_id' => $commentId,
            ])->send();
        }

        // 评论数量 +1，若是对评论的回复，字段为 reply_count，其他为 comment_count
        $model
            ->where("${table}_id", $commentableId)
            ->inc($table === 'comment' ? 'reply_count' : 'comment_count')
            ->update();

        return $commentId;
    }
}
