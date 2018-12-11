<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ServiceAbstracts;
use App\Constant\ErrorConstant;
use App\Exception\ApiException;
use App\Exception\ValidationException;
use App\Helper\HtmlHelper;
use App\Helper\MarkdownHelper;
use App\Helper\ValidatorHelper;
use App\Traits\CommentableTraits;
use App\Traits\FollowableTraits;
use App\Traits\BaseTraits;
use App\Traits\VotableTraits;

/**
 * 问题
 *
 * @property-read \App\Model\QuestionModel      currentModel
 *
 * Class QuestionService
 * @package App\Service
 */
class QuestionService extends ServiceAbstracts
{
    use BaseTraits, CommentableTraits, FollowableTraits, VotableTraits;

    /**
     * 获取隐私字段
     *
     * @return array
     */
    public function getPrivacyFields(): array
    {
        return ['delete_time'];
    }

    /**
     * 获取允许排序的字段
     *
     * @return array
     */
    public function getAllowOrderFields(): array
    {
        return ['vote_count', 'create_time', 'update_time'];
    }

    /**
     * 获取允许搜索的字段
     *
     * @return array
     */
    public function getAllowFilterFields(): array
    {
        return ['question_id', 'user_id'];
    }

    /**
     * 获取问题列表
     *
     * @param  bool  $withRelationship
     * @return array
     */
    public function getList(bool $withRelationship = false): array
    {
        $list = $this->questionModel
            ->where($this->getWhere())
            ->order($this->getOrder(['update_time' => 'DESC']))
            ->field($this->getPrivacyFields(), true)
            ->paginate();

        $list['data'] = $this->handle($list['data']);

        if ($withRelationship) {
            $list['data'] = $this->addRelationship($list['data']);
        }

        return $list;
    }

    /**
     * 根据用户ID获取问题列表
     *
     * @param  int   $userId
     * @param  bool  $withRelationship
     * @return array
     */
    public function getListByUserId(int $userId, bool $withRelationship = false): array
    {
        $this->userService->hasOrFail($userId);

        $list = $this->questionModel
            ->where(['user_id' => $userId])
            ->order($this->getOrder(['update_time' => 'DESC']))
            ->field($this->getPrivacyFields(), true)
            ->paginate();

        $list['data'] = $this->handle($list['data']);

        if ($withRelationship) {
            $list['data'] = $this->addRelationship($list['data']);
        }

        return $list;
    }

    /**
     * 发表问题
     *
     * @param  int    $userId          用户ID
     * @param  string $title           问题标题
     * @param  string $contentMarkdown Markdown 正文
     * @param  string $contentRendered HTML 正文
     * @param  array  $topicIds        话题ID数组
     * @return int    $questionId
     */
    public function create(
        int    $userId,
        string $title,
        string $contentMarkdown,
        string $contentRendered,
        array  $topicIds
    ): int
    {
        [
            $title,
            $contentMarkdown,
            $contentRendered,
            $topicIds
        ] = $this->createValidator(
            $title,
            $contentMarkdown,
            $contentRendered,
            $topicIds
        );

        // 添加问题
        $questionId = (int)$this->questionModel->insert([
            'user_id'          => $userId,
            'title'            => $title,
            'content_markdown' => $contentMarkdown,
            'content_rendered' => $contentRendered,
        ]);

        // 添加话题关系
        $topicable = [];
        foreach ($topicIds as $topicId) {
            $topicable[] = [
                'topic_id'       => $topicId,
                'topicable_id'   => $questionId,
                'topicable_type' => 'question',
            ];
        }
        if ($topicable) {
            $this->topicableModel->insert($topicable);
        }

        // 自动关注该问题
        $this->questionService->addFollow($userId, $questionId);

        // 用户的 question_count + 1
        $this->userModel
            ->where(['user_id' => $userId])
            ->update(['question_count[+]' => 1]);

        return $questionId;
    }

    /**
     * 发表问题前对参数进行验证
     *
     * @param  string $title
     * @param  string $contentMarkdown
     * @param  string $contentRendered
     * @param  array  $topicIds
     * @return array
     */
    private function createValidator(
        string $title,
        string $contentMarkdown,
        string $contentRendered,
        array  $topicIds
    ): array
    {
        $errors = [];

        // 验证标题
        if (!$title) {
            $errors['title'] = '标题不能为空';
        } elseif (!ValidatorHelper::isMin($title, 2)) {
            $errors['title'] = '标题长度不能小于 2 个字符';
        } elseif (!ValidatorHelper::isMax($title, 80)) {
            $errors['title'] = '标题长度不能超过 80 个字符';
        }

        // 验证正文不能为空
        $contentMarkdown = HtmlHelper::removeXss($contentMarkdown);
        $contentRendered = HtmlHelper::removeXss($contentRendered);

        // content_markdown 和 content_rendered 至少需传入一个；都传入时，以 content_markdown 为准
        if (!$contentMarkdown && !$contentRendered) {
            $errors['content_markdown'] = $errors['content_rendered'] = '正文不能为空';
        } elseif (!$contentMarkdown) {
            $contentMarkdown = HtmlHelper::toMarkdown($contentRendered);
        } else {
            $contentRendered = MarkdownHelper::toHtml($contentMarkdown);
        }

        // 验证正文长度
        if (
               !isset($errors['content_markdown'])
            && !isset($errors['content_rendered'])
            && !ValidatorHelper::isMax(strip_tags($contentRendered), 100000)
        ) {
            $errors['content_markdown'] = $errors['content_rendered'] = '正文不能超过 100000 个字';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }

        // 过滤不存在的 topic_id
        $isTopicIdsExist = $this->topicService->hasMultiple($topicIds);
        $topicIds = [];
        foreach ($isTopicIdsExist as $topicId => $isExist) {
            if ($isExist) {
                $topicIds[] = $topicId;
            }
        }

        return [$title, $contentMarkdown, $contentRendered, $topicIds];
    }

    /**
     * 更新问题
     *
     * @param  int    $questionId
     * @param  string $title
     * @param  string $contentMarkdown
     * @param  string $contentRendered
     * @param  array  $topicIds
     */
    public function update(
        int    $questionId,
        string $title = null,
        string $contentMarkdown = null,
        string $contentRendered = null,
        array  $topicIds = null
    ): void {
        [
            $data,
            $topicIds
        ] = $this->updateValidator(
            $questionId,
            $title,
            $contentMarkdown,
            $contentRendered,
            $topicIds
        );

        // 更新问题信息
        if ($data) {
            $this->questionModel
                ->where(['question_id' => $questionId])
                ->update($data);
        }

        // 更新话题关系
        if (!is_null($topicIds)) {
            $this->topicableModel
                ->where([
                    'topicable_id'   => $questionId,
                    'topicable_type' => 'question',
                ])
                ->delete();

            $topicable = [];
            foreach ($topicIds as $topicId) {
                $topicable[] = [
                    'topic_id'       => $topicId,
                    'topicable_id'   => $questionId,
                    'topicable_type' => 'question',
                ];
            }

            if ($topicable) {
                $this->topicableModel->insert($topicable);
            }
        }
    }

    /**
     * 更新问题前的字段验证
     *
     * @param  int    $questionId
     * @param  string $title
     * @param  string $contentMarkdown
     * @param  string $contentRendered
     * @param  array  $topicIds
     * @return array                   经过处理后的数据
     */
    private function updateValidator(
        int    $questionId,
        string $title = null,
        string $contentMarkdown = null,
        string $contentRendered = null,
        array  $topicIds = null
    ): array {
        $data = [];

        $userId = $this->roleService->userIdOrFail();
        $questionInfo = $this->questionModel->get($questionId);

        if (!$questionInfo) {
            throw new ApiException(ErrorConstant::QUESTION_NOT_FOUND);
        }

        if ($questionInfo['user_id'] != $userId && !$this->roleService->managerId()) {
            throw new ApiException(ErrorConstant::QUESTION_ONLY_AUTHOR_CAN_EDIT);
        }

        $errors = [];

        // 验证标题
        if (!is_null($title)) {
            if (!ValidatorHelper::isMin($title, 2)) {
                $errors['title'] = '标题长度不能小于 2 个字符';
            } elseif (!ValidatorHelper::isMax($title, 80)) {
                $errors['title'] = '标题长度不能超过 80 个字符';
            } else {
                $data['title'] = $title;
            }
        }

        // 验证正文
        if (!is_null($contentMarkdown) || !is_null($contentRendered)) {
            if (!is_null($contentMarkdown)) {
                $contentMarkdown = HtmlHelper::removeXss($contentMarkdown);
            }

            if (!is_null($contentRendered)) {
                $contentRendered = HtmlHelper::removeXss($contentRendered);
            }

            if (!$contentMarkdown && !$contentRendered) {
                $errors['content_markdown'] = $errors['content_rendered'] = '正文不能为空';
            } elseif (!$contentMarkdown) {
                $contentMarkdown = HtmlHelper::toMarkdown($contentRendered);
            } else {
                $contentRendered = MarkdownHelper::toHtml($contentMarkdown);
            }

            // 验证正文长度
            if (
                   !isset($errors['content_markdown'])
                && !isset($errors['content_rendered'])
                && !ValidatorHelper::isMax(strip_tags($contentRendered), 100000)
            ) {
                $errors['content_markdown'] = $errors['content_rendered'] = '正文不能超过 100000 个字';
            }

            if (!isset($errors['content_markdown']) && !isset($errors['content_rendered'])) {
                $data['content_markdown'] = $contentMarkdown;
                $data['content_rendered'] = $contentRendered;
            }
        }

        if ($errors) {
            throw new ValidationException($errors);
        }

        if (!is_null($topicIds)) {
            $isTopicIdsExist = $this->topicService->hasMultiple($topicIds);
            $topicIds = [];
            foreach ($isTopicIdsExist as $topicId => $isExist) {
                if ($isExist) {
                    $topicIds[] = $topicId;
                }
            }
        }

        return [$data, $topicIds];
    }

    /**
     * 删除问题
     *
     * @param  int  $questionId
     */
    public function delete(int $questionId): void
    {
        $userId = $this->roleService->userIdOrFail();
        $questionInfo = $this->questionModel->field('user_id')->get($questionId);

        if (!$questionInfo) {
            return;
        }

        if ($questionInfo['user_id'] != $userId && !$this->roleService->managerId()) {
            throw new ApiException(ErrorConstant::QUESTION_ONLY_AUTHOR_CAN_DELETE);
        }

        $this->questionModel->delete($questionId);

        // 该问题的作者的 question_count - 1
        $this->userModel
            ->where(['user_id' => $questionInfo['user_id']])
            ->update(['question_count[-]' => 1]);

        // 关注该问题的用户的 following_question_count - 1
        $followerIds = $this->followModel
            ->where(['followable_id' => $questionId, 'followable_type' => 'question'])
            ->pluck('user_id');

        $this->userModel
            ->where(['user_id' => $followerIds])
            ->update(['following_question_count[-]' => 1]);
    }

    /**
     * 批量软删除问题
     *
     * @param array $questionIds
     */
    public function batchDelete(array $questionIds): void
    {
        $questions = $this->questionModel
            ->field(['question_id', 'user_id'])
            ->where(['question_id' => $questionIds])
            ->select();

        if (!$questions) {
            return;
        }

        $questionIds = array_column($questions, 'question_id');
        $this->questionModel->delete($questionIds);

        $users = [];

        // 这些问题的作者的 question_count - 1
        foreach ($questions as $question) {
            isset($users[$question['user_id']]['question_count'])
                ? $users[$question['user_id']]['question_count'] += 1
                : $users[$question['user_id']]['question_count'] = 1;
        }

        // 关注这些问题的用户的 following_question_count - 1
        $followerIds = $this->followModel
            ->where(['followable_id' => $questionIds, 'followable_type' => 'question'])
            ->pluck('user_id');

        foreach ($followerIds as $followerId) {
            isset($users[$followerId]['following_question_count'])
                ? $users[$followerId]['following_question_count'] += 1
                : $users[$followerId]['following_question_count'] = 1;
        }

        foreach ($users as $userId => $user) {
            $update = [];

            if (isset($user['question_count'])) {
                $update['question_count[-]'] = $user['question_count'];
            }

            if (isset($user['following_question_count'])) {
                $update['following_question_count[-]'] = $user['following_question_count'];
            }

            $this->userModel
                ->where(['user_id' => $userId])
                ->update($update);
        }
    }

    /**
     * 对数据库中取出的问题信息进行处理
     *
     * @param  array $questions 提问信息，或多个提问组成的数组
     * @return array
     */
    public function handle(array $questions): array
    {
        if (!$questions) {
            return $questions;
        }

        if (!$isArray = is_array(current($questions))) {
            $questions = [$questions];
        }

        foreach ($questions as &$question) {
            // todo 处理提问
        }

        if ($isArray) {
            return $questions;
        }

        return $questions[0];
    }

    /**
     * 获取在 relationship 中使用的 question
     *
     * @param  array $questionIds
     * @return array
     */
    public function getInRelationship(array $questionIds): array
    {
        $questions = array_combine($questionIds, array_fill(0, count($questionIds), []));

        $questionsTmp = $this->questionModel
            ->field(['question_id', 'title', 'create_time', 'update_time'])
            ->select($questionIds);

        foreach ($questionsTmp as $item) {
            $questions[$item['question_id']] = $item;
        }

        return $questions;
    }

    /**
     * 为问题添加 relationship 字段
     * {
     *     user: {}
     *     topics: [ {}, {}, {} ]
     *     is_following: false
     *     voting: up、down、''
     * }
     *
     * @param  array $questions
     * @param  array $relationship ['is_following': bool]
     * @return array
     */
    public function addRelationship(array $questions, array $relationship = []): array
    {
        if (!$questions) {
            return $questions;
        }

        if (!$isArray = is_array(current($questions))) {
            $questions = [$questions];
        }

        $questionIds = array_unique(array_column($questions, 'question_id'));
        $userIds = array_unique(array_column($questions, 'user_id'));

        if (isset($relationship['is_following'])) {
            $followingQuestionIds = $relationship['is_following'] ? $questionIds : [];
        } else {
            $followingQuestionIds = $this->followService->getIsFollowingInRelationship($questionIds, 'question');
        }

        $votings = $this->voteService->getVotingInRelationship($questionIds, 'question');
        $users = $this->userService->getInRelationship($userIds);
        $topics = $this->topicService->getInRelationship($questionIds, 'question');

        // 合并数据
        foreach ($questions as &$question) {
            $question['relationship'] = [
                'user'         => $users[$question['user_id']],
                'topics'       => $topics[$question['question_id']],
                'is_following' => in_array($question['question_id'], $followingQuestionIds),
                'voting'       => $votings[$question['question_id']],
            ];
        }

        if ($isArray) {
            return $questions;
        }

        return $questions[0];
    }
}
