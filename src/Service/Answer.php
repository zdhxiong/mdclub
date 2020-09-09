<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Facade\Library\Auth;
use MDClub\Facade\Model\AnswerModel;
use MDClub\Facade\Model\CommentModel;
use MDClub\Facade\Model\ImageModel;
use MDClub\Facade\Model\QuestionModel;
use MDClub\Facade\Model\ReportModel;
use MDClub\Facade\Model\UserModel;
use MDClub\Facade\Model\VoteModel;
use MDClub\Facade\Service\CommentService;
use MDClub\Facade\Service\ImageService;
use MDClub\Facade\Service\NotificationService;
use MDClub\Facade\Service\QuestionService;
use MDClub\Facade\Service\UserService;
use MDClub\Facade\Validator\AnswerValidator;
use MDClub\Service\Interfaces\CommentableInterface;
use MDClub\Service\Interfaces\DeletableInterface;
use MDClub\Service\Interfaces\GetableInterface;
use MDClub\Service\Interfaces\VotableInterface;
use MDClub\Service\Traits\Commentable;
use MDClub\Service\Traits\Deletable;
use MDClub\Service\Traits\Getable;
use MDClub\Service\Traits\Votable;

/**
 * 回答服务
 */
class Answer extends Abstracts implements CommentableInterface, DeletableInterface, GetableInterface, VotableInterface
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
        return \MDClub\Model\Answer::class;
    }

    /**
     * 根据 user_id 获取回答列表
     *
     * @param  int   $userId
     * @return array
     */
    public function getByUserId(int $userId): array
    {
        UserService::hasOrFail($userId);

        return AnswerModel::getByUserId($userId);
    }

    /**
     * 根据 question_id 获取回答列表
     *
     * @param  int   $questionId
     * @return array
     */
    public function getByQuestionId(int $questionId): array
    {
        QuestionService::hasOrFail($questionId);

        return AnswerModel::getByQuestionId($questionId);
    }

    /**
     * 发表回答
     *
     * @param  int   $questionId 提问ID
     * @param  array $data       [content_markdown, content_rendered]
     * @return int               回答ID
     */
    public function create(int $questionId, array $data): int
    {
        $question = QuestionService::getOrFail($questionId);
        $userId = Auth::userId();

        $createData = AnswerValidator::create($data);
        $createData['user_id'] = $userId;
        $createData['question_id'] = $questionId;

        $answerId = (int) AnswerModel::insert($createData);

        NotificationService::add($question['user_id'], 'question_answered', [
            'question_id' => $questionId,
            'answer_id' => $answerId,
        ])->send();
        UserModel::incAnswerCount($userId);
        QuestionModel::incAnswerCount($questionId);
        ImageService::updateItemRelated('answer', $answerId, $createData['content_markdown']);

        return $answerId;
    }

    /**
     * 更新回答
     *
     * @param int   $answerId
     * @param array $data     [content_markdown, content_rendered]
     */
    public function update(int $answerId, array $data): void
    {
        $updateData = AnswerValidator::update($answerId, $data);

        if (!isset($updateData['content_markdown'])) {
            return;
        }

        AnswerModel
            ::where('answer_id', $answerId)
            ->set('content_markdown', $updateData['content_markdown'])
            ->set('content_rendered', $updateData['content_rendered'])
            ->update();
    }

    /**
     * @inheritDoc
     */
    public function delete(int $answerId): void
    {
        $answer = AnswerValidator::delete($answerId);

        if (!$answer) {
            return;
        }

        $this->traitDelete($answerId, $answer);
    }

    /**
     * 删除回答后的操作
     *
     * @param array $answers
     * @param bool $callByParent 是否由于父元素被删触发的该方法。
     *                           为 true 时，由于父元素已不存在，不需要对父元素进行操作
     *                           父元素为 question
     */
    public function afterDelete(array $answers, bool $callByParent = false): void
    {
        if (!$answers) {
            return;
        }

        $answerIds = array_column($answers, 'answer_id');

        ReportModel::deleteByAnswerIds($answerIds);
        VoteModel::deleteByAnswerIds($answerIds);

        $comments = CommentModel
            ::where('commentable_type', 'answer')
            ->where('commentable_id', $answerIds)
            ->force()
            ->select();

        CommentModel::deleteByAnswerIds($answerIds);
        CommentService::afterDelete($comments, true);

        // 减少用户的 answer_count
        $users = [];

        foreach ($answers as $answer) {
            $userId = $answer['user_id'];

            isset($users[$userId])
                ? $users[$userId] += 1
                : $users[$userId] = 1;
        }

        foreach ($users as $userId => $count) {
            UserModel::decAnswerCount($userId, $count);
        }

        // 删除图片文件
        $images = ImageModel
            ::where('item_type', 'answer')
            ->where('item_id', $answerIds)
            ->select();
        ImageService::doDelete($images);

        if ($callByParent) {
            return;
        }

        // 减少提问的 answer_count
        $questions = [];

        foreach ($answers as $answer) {
            $questionId = $answer['question_id'];

            isset($questions[$questionId])
                ? $questions[$questionId] += 1
                : $questions[$questionId] = 1;
        }

        foreach ($questions as $questionId => $count) {
            QuestionModel::decAnswerCount($questionId, $count);
        }
    }
}
