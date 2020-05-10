<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Facade\Library\Auth;
use MDClub\Facade\Model\AnswerModel;
use MDClub\Facade\Model\CommentModel;
use MDClub\Facade\Model\FollowModel;
use MDClub\Facade\Model\QuestionModel;
use MDClub\Facade\Model\ReportModel;
use MDClub\Facade\Model\TopicableModel;
use MDClub\Facade\Model\TopicModel;
use MDClub\Facade\Model\UserModel;
use MDClub\Facade\Model\VoteModel;
use MDClub\Facade\Service\AnswerService;
use MDClub\Facade\Service\CommentService;
use MDClub\Facade\Service\ImageService;
use MDClub\Facade\Service\TopicService;
use MDClub\Facade\Service\UserService;
use MDClub\Facade\Validator\QuestionValidator;
use MDClub\Service\Interfaces\CommentableInterface;
use MDClub\Service\Interfaces\DeletableInterface;
use MDClub\Service\Interfaces\FollowableInterface;
use MDClub\Service\Interfaces\GetableInterface;
use MDClub\Service\Interfaces\VotableInterface;
use MDClub\Service\Traits\Commentable;
use MDClub\Service\Traits\Deletable;
use MDClub\Service\Traits\Followable;
use MDClub\Service\Traits\Getable;
use MDClub\Service\Traits\Votable;

/**
 * 提问服务
 */
class Question extends Abstracts implements
    CommentableInterface,
    DeletableInterface,
    FollowableInterface,
    GetableInterface,
    VotableInterface
{
    use Commentable;
    use Deletable;
    use Followable;
    use Getable;
    use Votable;

    /**
     * @inheritDoc
     */
    protected function getModel(): string
    {
        return \MDClub\Model\Question::class;
    }

    /**
     * 根据 user_id 获取提问列表
     *
     * @param int $userId
     *
     * @return array
     */
    public function getByUserId(int $userId): array
    {
        UserService::hasOrFail($userId);

        return QuestionModel::getByUserId($userId);
    }

    /**
     * 根据 topic_id 获取提问列表
     *
     * @param int $topicId
     *
     * @return array
     */
    public function getByTopicId(int $topicId): array
    {
        TopicService::hasOrFail($topicId);

        return QuestionModel::getByTopicId($topicId);
    }

    /**
     * 发表提问
     *
     * @param array $data [title, content_markdown, content_rendered, topic_id]
     *
     * @return int         主键值
     */
    public function create(array $data): int
    {
        $userId = Auth::userId();
        $createData = QuestionValidator::create($data);

        $questionId = (int)QuestionModel
            ::set('user_id', $userId)
            ->set('title', $createData['title'])
            ->set('content_markdown', $createData['content_markdown'])
            ->set('content_rendered', $createData['content_rendered'])
            ->insert();

        $this->updateTopicable($questionId, $createData['topic_ids']);

        UserModel::incQuestionCount($userId);
        ImageService::updateItemRelated('question', $questionId, $createData['content_markdown']);

        return $questionId;
    }

    /**
     * 更新提问
     *
     * @param int   $questionId
     * @param array $data [title, content_markdown, content_rendered, topic_id]
     */
    public function update(int $questionId, array $data): void
    {
        $updateDate = QuestionValidator::update($questionId, $data);

        if (!$updateDate) {
            return;
        }

        if (isset($updateDate['title']) || isset($updateDate['content_markdown'])) {
            QuestionModel::where('question_id', $questionId);

            if (isset($updateDate['title'])) {
                QuestionModel::set('title', $updateDate['title']);
            }

            if (isset($updateDate['content_markdown'])) {
                QuestionModel
                    ::set('content_markdown', $updateDate['content_markdown'])
                    ->set('content_rendered', $updateDate['content_rendered']);
            }

            QuestionModel::update();
        }

        if (isset($updateDate['topic_ids'])) {
            $this->updateTopicable($questionId, $updateDate['topic_ids'], true);
        }
    }

    /**
     * 更新话题关系
     *
     * @param int   $questionId 提问ID
     * @param array $topicIds   话题ID数组
     * @param bool  $removeOld  是否要移除旧的话题关系
     */
    protected function updateTopicable(int $questionId, array $topicIds, bool $removeOld = false): void
    {
        // 移除旧的话题关系
        if ($removeOld) {
            $existTopicIds = TopicableModel
                ::where('topicable_type', 'question')
                ->where('topicable_id', $questionId)
                ->pluck('topic_id');

            $needDeleteTopicIds = array_diff($existTopicIds, $topicIds);
            if ($needDeleteTopicIds) {
                TopicableModel
                    ::where('topicable_type', 'question')
                    ->where('topicable_id', $questionId)
                    ->where('topic_id', $needDeleteTopicIds)
                    ->delete();
                TopicModel::decQuestionCount($needDeleteTopicIds);
            }

            $topicIds = array_diff($topicIds, $existTopicIds);
        }

        // 添加新话题关系
        $topicable = [];
        foreach ($topicIds as $topicId) {
            $topicable[] = [
                'topic_id' => $topicId,
                'topicable_id' => $questionId,
                'topicable_type' => 'question',
            ];
        }
        if ($topicable) {
            TopicableModel::insert($topicable);
            TopicModel::incQuestionCount($topicIds);
        }
    }

    /**
     * @inheritDoc
     */
    public function delete(int $questionId): void
    {
        $question = QuestionValidator::delete($questionId);

        if (!$question) {
            return;
        }

        $this->traitDelete($questionId, $question);
    }

    /**
     * 删除提问后的操作
     *
     * @param array $questions
     */
    public function afterDelete(array $questions): void
    {
        if (!$questions) {
            return;
        }

        $questionIds = array_column($questions, 'question_id');

        // 作者的 question_count - 1, 关注者的 following_question_count - 1
        // [ user_id => [ question_count => count, following_question_count => count ] ]
        $users = [];

        foreach ($questions as $question) {
            $userId = $question['user_id'];

            isset($users[$userId]['question_count'])
                ? $users[$userId]['question_count'] += 1
                : $users[$userId]['question_count'] = 1;
        }

        $followerIds = FollowModel
            ::where('followable_type', 'question')
            ->where('followable_id', $questionIds)
            ->pluck('user_id');

        foreach ($followerIds as $followerId) {
            isset($users[$followerId]['following_question_count'])
                ? $users[$followerId]['following_question_count'] += 1
                : $users[$followerId]['following_question_count'] = 1;
        }

        foreach ($users as $userId => $user) {
            if (isset($user['question_count'])) {
                UserModel::dec('question_count', $user['question_count']);
            }

            if (isset($user['following_question_count'])) {
                UserModel::dec('following_question_count', $user['following_question_count']);
            }

            UserModel::where('user_id', $userId)->update();
        }

        // 提问所属的话题的 question_count - 1
        // [ topic_id => count ]
        $topics = [];
        $topicIds = TopicableModel
            ::where('topicable_type', 'question')
            ->where('topicable_id', $questionIds)
            ->pluck('topic_id');

        foreach ($topicIds as $topicId) {
            isset($topics[$topicId])
                ? $topics[$topicId] += 1
                : $topics[$topicId] = 1;
        }

        foreach ($topics as $topicId => $count) {
            TopicModel::decQuestionCount($topicId, $count);
        }

        // 删除其他数据
        FollowModel::deleteByQuestionIds($questionIds);
        ReportModel::deleteByQuestionIds($questionIds);
        VoteModel::deleteByQuestionIds($questionIds);
        TopicableModel::deleteByQuestionIds($questionIds);

        $answers = AnswerModel
            ::where('question_id', $questionIds)
            ->force()
            ->select();

        AnswerModel::where('question_id', $questionIds)->force()->delete();
        AnswerService::afterDelete($answers, true);

        $comments = CommentModel
            ::where('commentable_type', 'question')
            ->where('commentable_id', $questionIds)
            ->force()
            ->select();

        CommentModel::deleteByQuestionIds($questionIds);
        CommentService::afterDelete($comments, true);
    }
}
