<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Facade\Model\AnswerModel;
use MDClub\Facade\Model\ArticleModel;
use MDClub\Facade\Model\CommentModel;
use MDClub\Facade\Model\QuestionModel;
use MDClub\Facade\Model\ReportModel;
use MDClub\Facade\Model\VoteModel;
use MDClub\Facade\Service\UserService;
use MDClub\Facade\Validator\CommentValidator;
use MDClub\Service\Interfaces\DeletableInterface;
use MDClub\Service\Interfaces\GetableInterface;
use MDClub\Service\Interfaces\VotableInterface;
use MDClub\Service\Traits\Commentable;
use MDClub\Service\Traits\Deletable;
use MDClub\Service\Traits\Getable;
use MDClub\Service\Traits\Votable;

/**
 * 评论服务
 */
class Comment extends Abstracts implements DeletableInterface, GetableInterface, VotableInterface
{
    use Commentable;
    use Deletable;
    use Getable;
    use Votable;

    /**
     * @inheritDoc
     */
    protected function getModel(): string
    {
        return \MDClub\Model\Comment::class;
    }

    /**
     * 根据 user_id 获取评论列表
     *
     * @param  int   $userId
     * @return array
     */
    public function getByUserId(int $userId): array
    {
        UserService::hasOrFail($userId);

        return CommentModel::getByUserId($userId);
    }

    /**
     * 更新评论
     *
     * @param  int   $commentId
     * @param  array $data [content]
     */
    public function update(int $commentId, array $data): void
    {
        $updateData = CommentValidator::update($commentId, $data);

        if (!isset($updateData['content'])) {
            return;
        }

        CommentModel
            ::where('comment_id', $commentId)
            ->update('content', $updateData['content']);
    }

    /**
     * @inheritDoc
     */
    public function delete(int $commentId): void
    {
        $comment = CommentValidator::delete($commentId);

        if (!$comment) {
            return;
        }

        $this->traitDelete($commentId, $comment);
    }

    /**
     * 删除评论后的操作
     *
     * @param array $comments
     * @param bool $callByParent 是否由于父元素被删触发的该方法
     *                           为 true 时，由于父元素已不存在，不需要对父元素进行操作
     *                           父元素可以为 article, question, answer
     */
    public function afterDelete(array $comments, bool $callByParent = false): void
    {
        if (!$comments) {
            return;
        }

        $commentIds = array_column($comments, 'comment_id');

        ReportModel::deleteByCommentIds($commentIds);
        VoteModel::deleteByCommentIds($commentIds);

        if ($callByParent) {
            return;
        }

        // [ commentableType => [ commentableId => count ] ]
        $targets = [];

        foreach ($comments as $comment) {
            $type = $comment['commentable_type'];
            $id = $comment['commentable_id'];

            isset($targets[$type])
                ? null
                : $targets[$type] = [];

            isset($targets[$type][$id])
                ? $targets[$type][$id] += 1
                : $targets[$type][$id] = 1;
        }

        foreach ($targets as $type => $target) {
            foreach ($target as $targetId => $count) {
                switch ($type) {
                    case 'question':
                        QuestionModel::decCommentCount($targetId, $count);
                        break;
                    case 'article':
                        ArticleModel::decCommentCount($targetId, $count);
                        break;
                    case 'answer':
                        AnswerModel::decCommentCount($targetId, $count);
                        break;
                }
            }
        }
    }
}
