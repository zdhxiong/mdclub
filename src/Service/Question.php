<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ServiceAbstracts;
use App\Constant\ErrorConstant;
use App\Exception\ApiException;
use App\Exception\ValidationException;
use App\Helper\ArrayHelper;
use App\Helper\HtmlHelper;
use App\Helper\MarkdownHelper;
use App\Helper\ValidatorHelper;
use App\Traits\Commentable;
use App\Traits\Followable;
use App\Traits\Base;
use App\Traits\Votable;

/**
 * 提问
 *
 * @property-read \App\Model\Question      currentModel
 *
 * Class Question
 * @package App\Service
 */
class Question extends ServiceAbstracts
{
    use Base, Commentable, Followable, Votable;

    /**
     * 获取隐私字段
     *
     * @return array
     */
    public function getPrivacyFields(): array
    {
        return $this->container->roleService->managerId()
            ? []
            : ['delete_time'];
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
        return ['question_id', 'user_id', 'topic_id']; // topic_id 需要另外写逻辑
    }

    /**
     * 获取提问列表
     *
     * @param  array $condition
     * 两个参数中仅可指定一个
     * [
     *     'user_id'    => '',
     *     'topic_id'   => '',
     *     'is_deleted' => true, // 该值为 true 时，获取已删除的记录；否则获取未删除的记录
     * ]
     * @param  bool  $withRelationship
     * @return array
     */
    public function getList(array $condition = [], bool $withRelationship = false): array
    {
        $join = null;
        $where = [];
        $order = $this->getOrder(['update_time' => 'DESC']);

        if (isset($condition['user_id'])) {
            $this->container->userService->hasOrFail($condition['user_id']);
            $where['user_id'] = $condition['user_id'];
        }

        elseif (isset($condition['topic_id'])) {
            $this->container->topicService->hasOrFail($condition['topic_id']);
            $join = ['[><]topicable' => ['question_id' => 'topicable_id']];
            $where['topicable.topic_id'] = $condition['topic_id'];
            $where['topicable.topicable_type'] = 'question';
        }

        // 根据 query 参数获取提问列表，参数包括 question_id、user_id、topic_id
        else {
            $where = $this->getWhere();

            if (isset($where['topic_id'])) {
                $join = ['[><]topicable' => ['question_id' => 'topicable_id']];
                $where['topicable.topic_id'] = $where['topic_id'];
                $where['topicable.topicable_type'] = 'question';
                unset($where['topic_id']);
            }

            if (isset($where['user_id'])) {
                $where['question.user_id'] = $where['user_id'];
                unset($where['user_id']);
            }

            if (isset($where['question_id'])) {
                $where['question.question_id'] = $where['question_id'];
                unset($where['question_id']);
            }

            // 获取已删除的列表
            if (isset($condition['is_deleted']) && $condition['is_deleted']) {
                $this->container->questionModel->onlyTrashed();

                $defaultOrder = ['delete_time' => 'DESC'];
                $allowOrderFields = ArrayHelper::push($this->getAllowOrderFields(), 'delete_time');
                $order = $this->getOrder($defaultOrder, $allowOrderFields);
            }
        }

        $list = $this->container->questionModel
            ->join($join)
            ->where($where)
            ->order($order)
            ->field($this->getPrivacyFields(), true)
            ->paginate();

        $list['data'] = $this->handle($list['data']);

        if ($withRelationship) {
            $list['data'] = $this->addRelationship($list['data']);
        }

        return $list;
    }

    /**
     * 发表提问
     *
     * @param  int    $userId          用户ID
     * @param  string $title           提问标题
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
        array  $topicIds = null
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

        // 添加提问
        $questionId = (int)$this->container->questionModel->insert([
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
            $this->container->topicableModel->insert($topicable);
        }

        // 自动关注该提问
        $this->container->questionService->addFollow($userId, $questionId);

        // 用户的 question_count + 1
        $this->container->userModel
            ->where(['user_id' => $userId])
            ->update(['question_count[+]' => 1]);

        return $questionId;
    }

    /**
     * 发表提问前对参数进行验证
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
        array  $topicIds = null
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

        if (is_null($topicIds)) {
            $topicIds = [];
        }

        // 过滤不存在的 topic_id
        $isTopicIdsExist = $this->container->topicService->hasMultiple($topicIds);
        $topicIds = [];
        foreach ($isTopicIdsExist as $topicId => $isExist) {
            if ($isExist) {
                $topicIds[] = $topicId;
            }
        }

        return [$title, $contentMarkdown, $contentRendered, $topicIds];
    }

    /**
     * 更新提问
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

        // 更新提问信息
        if ($data) {
            $this->container->questionModel
                ->where(['question_id' => $questionId])
                ->update($data);
        }

        // 更新话题关系
        if (!is_null($topicIds)) {
            $this->container->topicableModel
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
                $this->container->topicableModel->insert($topicable);
            }
        }
    }

    /**
     * 更新提问前的字段验证
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

        $userId = $this->container->roleService->userIdOrFail();
        $questionInfo = $this->container->questionModel->get($questionId);

        if (!$questionInfo) {
            throw new ApiException(ErrorConstant::QUESTION_NOT_FOUND);
        }

        if ($questionInfo['user_id'] != $userId && !$this->container->roleService->managerId()) {
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
            $isTopicIdsExist = $this->container->topicService->hasMultiple($topicIds);
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
     * 软删除提问
     *
     * @param  int  $questionId
     */
    public function delete(int $questionId): void
    {
        $userId = $this->container->roleService->userIdOrFail();
        $questionInfo = $this->container->questionModel->field('user_id')->get($questionId);

        if (!$questionInfo) {
            return;
        }

        if ($questionInfo['user_id'] != $userId && !$this->container->roleService->managerId()) {
            throw new ApiException(ErrorConstant::QUESTION_ONLY_AUTHOR_CAN_DELETE);
        }

        $this->container->questionModel->delete($questionId);

        // 该提问的作者的 question_count - 1
        $this->container->userModel
            ->where(['user_id' => $questionInfo['user_id']])
            ->update(['question_count[-]' => 1]);

        // 关注该提问的用户的 following_question_count - 1
        $followerIds = $this->container->followModel
            ->where(['followable_id' => $questionId, 'followable_type' => 'question'])
            ->pluck('user_id');
        $this->container->userModel
            ->where(['user_id' => $followerIds])
            ->update(['following_question_count[-]' => 1]);
    }

    /**
     * 批量软删除提问
     *
     * @param array $questionIds
     */
    public function deleteMultiple(array $questionIds): void
    {
        if (!$questionIds) {
            return;
        }

        $questions = $this->container->questionModel
            ->field(['question_id', 'user_id'])
            ->select($questionIds);

        if (!$questions) {
            return;
        }

        $questionIds = array_column($questions, 'question_id');
        $this->container->questionModel->delete($questionIds);

        $users = [];

        // 这些提问的作者的 question_count - 1
        foreach ($questions as $question) {
            isset($users[$question['user_id']]['question_count'])
                ? $users[$question['user_id']]['question_count'] += 1
                : $users[$question['user_id']]['question_count'] = 1;
        }

        // 关注这些提问的用户的 following_question_count - 1
        $followerIds = $this->container->followModel
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

            $this->container->userModel
                ->where(['user_id' => $userId])
                ->update($update);
        }
    }

    /**
     * 恢复文章
     *
     * @param int $questionId
     */
    public function restore(int $questionId): void
    {

    }

    /**
     * 批量恢复文章
     *
     * @param array $questionIds
     */
    public function restoreMultiple(array $questionIds): void
    {

    }

    /**
     * 硬删除文章
     *
     * @param int $questionId
     */
    public function destroy(int $questionId): void
    {

    }

    /**
     * 批量硬删除文章
     *
     * @param array $questionIds
     */
    public function destroyMultiple(array $questionIds): void
    {

    }

    /**
     * 对数据库中取出的提问信息进行处理
     * todo 处理提问
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

        $questionsTmp = $this->container->questionModel
            ->field(['question_id', 'title', 'create_time', 'update_time'])
            ->select($questionIds);

        foreach ($questionsTmp as $item) {
            $questions[$item['question_id']] = $item;
        }

        return $questions;
    }

    /**
     * 为提问添加 relationship 字段
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
            $followingQuestionIds = $this->container->followService->getIsFollowingInRelationship($questionIds, 'question');
        }

        $votings = $this->container->voteService->getVotingInRelationship($questionIds, 'question');
        $users = $this->container->userService->getInRelationship($userIds);
        $topics = $this->container->topicService->getInRelationship($questionIds, 'question');

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
