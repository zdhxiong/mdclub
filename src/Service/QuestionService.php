<?php

declare(strict_types=1);

namespace App\Service;

use App\Constant\ErrorConstant;
use App\Exception\ApiException;
use App\Exception\ValidationException;
use App\Helper\ArrayHelper;
use App\Helper\HtmlHelper;
use App\Helper\MarkdownHelper;
use App\Helper\ValidatorHelper;
use App\Interfaces\FollowableInterface;

/**
 * 问题
 *
 * Class QuestionService
 * @package App\Service
 */
class QuestionService extends Service implements FollowableInterface
{
    /**
     * 获取隐私字段
     *
     * @return array
     */
    protected function getPrivacyFields(): array
    {
        return $this->roleService->managerId()
            ? []
            : [
                'delete_time',
            ];
    }

    /**
     * 获取允许排序的字段
     *
     * @return array
     */
    protected function getAllowOrderFields(): array
    {
        return $this->roleService->managerId()
            ? [
                'question_id',
                'user_id',
                'answer_count',
                'view_count',
                'follower_count',
                'last_answer_time',
                'create_time',
                'update_time',
                'delete_time',
            ]
            : [];
    }

    /**
     * 获取允许搜索的字段
     *
     * @return array
     */
    protected function getAllowFilterFields(): array
    {
        return $this->roleService->managerId()
            ? [
                'question_id',
                'user_id',
                'delete_time',
            ]
            : [];
    }

    /**
     * 获取用户列表
     *
     * @param  bool $withRelationship
     * @return array
     */
    public function getList($withRelationship = false): array
    {
        $excludeFields = $this->optionService->get('enable_markdown') ? [] : ['content_markdown'];
        $excludeFields = ArrayHelper::push($excludeFields, $this->getPrivacyFields());

        $list = $this->questionModel
            ->where($this->getWhere())
            ->order($this->getOrder(['question_id' => 'DESC']))
            ->field($excludeFields, true)
            ->paginate();

        if ($withRelationship) {
            $list['data'] = $this->addRelationship($list['data']);
        }

        return $list;
    }

    /**
     * 获取最近更新的问题列表
     *
     * @param  bool  $withRelationship
     * @return array
     */
    public function getRecent($withRelationship = false): array
    {
        $excludeFields = $this->optionService->get('enable_markdown') ? [] : ['content_markdown'];
        $excludeFields = ArrayHelper::push($excludeFields, $this->getPrivacyFields());

        $list = $this->questionModel
            ->field($excludeFields, true)
            ->order(['update_time' => 'DESC'])
            ->paginate();

        if ($withRelationship) {
            $list['data'] = $this->addRelationship($list['data']);
        }

        return $list;
    }

    /**
     * 获取最近热门问题列表
     *
     * @param  bool  $withRelationship
     * @return array
     */
    public function getPopular($withRelationship = false): array
    {
        $excludeFields = $this->optionService->get('enable_markdown') ? [] : ['content_markdown'];
        $excludeFields = ArrayHelper::push($excludeFields, $this->getPrivacyFields());

        $list = $this->questionModel
            ->field($excludeFields, true)
            ->order([
                'follower_count' => 'DESC',
                'update_time' => 'DESC',
            ])
            ->paginate();

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
        $this->questionFollowService->addFollow($userId, $questionId);

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
        $isEnableMarkdown = $this->optionService->get('enable_markdown');

        if ($isEnableMarkdown) {
            // 启用 markdown 时，content_markdown 和 content_rendered 至少需传入一个；都传入时，以 content_markdown 为准
            if (!$contentMarkdown && !$contentRendered) {
                $errors['content_markdown'] = $errors['content_rendered'] = '正文不能为空';
            } elseif (!$contentMarkdown) {
                $contentMarkdown = HtmlHelper::toMarkdown($contentRendered);
            } else {
                $contentRendered = MarkdownHelper::toHtml($contentMarkdown);
            }
        } else {
            // 禁用 markdown 时，必须传入 content_rendered
            if (!$contentRendered) {
                $errors['content_rendered'] = '正文不能为空';
            } else {
                $contentMarkdown = HtmlHelper::toMarkdown($contentRendered);
            }
        }

        // 验证正文长度
        if (!$errors && !ValidatorHelper::isMax(strip_tags($contentRendered), 100000)) {
            $errors['content_markdown'] = $errors['content_rendered'] = '正文不能超过 100000 个字';

            if (!$isEnableMarkdown) {
                unset($errors['content_markdown']);
            }
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
     * 判断指定问题是否存在
     *
     * @param  int  $questionId
     * @return bool
     */
    public function has(int $questionId): bool
    {
        return $this->questionModel->has($questionId);
    }

    /**
     * 获取问题信息
     *
     * @param  int  $questionId
     * @param  bool $withRelationship
     * @return array
     */
    public function get(int $questionId, bool $withRelationship = false): array
    {
        $excludeFields = $this->optionService->get('enable_markdown') ? [] : ['content_markdown'];
        $excludeFields = ArrayHelper::push($excludeFields, $this->getPrivacyFields());
        $questionInfo = $this->questionModel->field($excludeFields, true)->get($questionId);

        if (!$questionInfo) {
            throw new ApiException(ErrorConstant::QUESTION_NOT_FOUND);
        }

        if ($withRelationship) {
            $questionInfo = $this->addRelationship($questionInfo);
        }

        return $questionInfo;
    }

    /**
     * 获取多个问题信息
     *
     * @param  array $questionIds
     * @param  bool  $withRelationship
     * @return array
     */
    public function getMultiple(array $questionIds, bool $withRelationship = false): array
    {
        if (!$questionIds) {
            return [];
        }

        $excludeFields = $this->optionService->get('enable_markdown') ? [] : ['content_markdown'];
        $excludeFields = ArrayHelper::push($excludeFields, $this->getPrivacyFields());
        $questions = $this->questionModel
            ->where(['question_id' => $questionIds])
            ->field($excludeFields, true)
            ->select();

        foreach ($questions as &$question) {
            $question = $this->handle($question);
        }

        if ($withRelationship) {
            $questions = $this->addRelationship($questions);
        }

        return $questions;
    }

    /**
     * 删除问题
     *
     * @param  int  $questionId
     * @param  bool $softDelete
     * @return bool
     */
    public function delete(int $questionId, bool $softDelete): bool
    {
        // 删除问题
        if (!$softDelete) {
            $this->questionModel->force();
        }

        $rowCount = $this->questionModel->delete($questionId);
        if (!$rowCount) {
            throw new ApiException(ErrorConstant::QUESTION_NOT_FOUND);
        }

        // TODO 删除问题后更新关联数据

        if (!$softDelete) {
            // 删除关联的话题
            $this->topicableModel
                ->where([
                    'topicable_id'   => $questionId,
                    'topicable_type' => 'question',
                ])
                ->delete();

            // 删除问题下的评论
            $this->commentModel
                ->where([
                    'commentable_id' => $questionId,
                    'commentable_type' => 'question',
                ])
                ->force()
                ->delete();

            // 删除问题下的回答
            $answers = $this->answerModel
                ->where([
                    'question_id' => $questionId,
                ])->select();
            $this->answerModel
                ->where([
                    ''
                ]);

            // 删除回答下的所有评论



            // 删除问题关注记录

        }

        return true;
    }



    /**
     * 对数据库中读取的数据进行处理
     *
     * @param  array $questionInfo
     * @return array
     */
    public function handle(array $questionInfo): array
    {
        if (!$questionInfo) {
            return $questionInfo;
        }

        // todo

        return $questionInfo;
    }

    /**
     * 为问题添加 relationship 字段
     * {
     *     user: {
     *         user_id: '',
     *         username: '',
     *         headline: '',
     *         avatar: {
     *             s: '',
     *             m: '',
     *             l: ''
     *         }
     *     }
     *     topic: [
     *         {
     *             name: '',
     *             cover: {
     *                 s: '',
     *                 m: '',
     *                 l: ''
     *             }
     *         }
     *     ]
     *     is_following: false
     * }
     *
     * @param  array $questions
     * @param  array $relationship
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

        $currentUserId = $this->roleService->userId();
        $questionIds = array_unique(array_column($questions, 'question_id'));
        $userIds = array_unique(array_column($questions, 'user_id'));
        $followingQuestionIds = [];
        $users = [];
        $topics = [];

        // is_following
        if ($currentUserId) {
            if (isset($relationship['is_following'])) {
                $followingQuestionIds = $relationship['is_following'] ? $questionIds : [];
            } else {
                $followingQuestionIds = $this->followableModel->where([
                    'user_id'         => $currentUserId,
                    'followable_id'   => $questionIds,
                    'followable_type' => 'question',
                ])->pluck('followable_id');
            }
        }

        // user
        $usersTmp = $this->userModel
            ->where(['user_id' => $userIds])
            ->field(['user_id', 'avatar', 'username', 'headline'])
            ->select();
        foreach ($usersTmp as $item) {
            $item = $this->userService->handle($item);
            $users[$item['user_id']] = [
                'user_id'  => $item['user_id'],
                'username' => $item['username'],
                'headline' => $item['headline'],
                'avatar'   => $item['avatar'],
            ];
        }

        // topic
        $topicsTmp = $this->topicModel
            ->join([
                '[><]topicable' => ['topic_id' => 'topic_id']
            ])
            ->where([
                'topicable.topicable_id' => $questionIds,
                'topicable.topicable_type' => 'question',
            ])
            ->order(['topicable.create_time' => 'ASC'])
            ->field(['topic.topic_id', 'topic.name', 'topic.cover', 'topicable.topicable_id(question_id)'])
            ->select();
        foreach ($questionIds as $questionIdTmp) {
            $topics[$questionIdTmp] = [];
        }
        foreach ($topicsTmp as $item) {
            $topics[$item['question_id']][] = $this->topicService->handle([
                'topic_id' => $item['topic_id'],
                'name' => $item['name'],
                'cover' => $item['cover'],
            ]);
        }

        foreach ($questions as &$question) {
            $question['relationship'] = [
                'user'         => $users[$question['user_id']],
                'topic'        => $topics[$question['question_id']],
                'is_following' => in_array($question['question_id'], $followingQuestionIds),
            ];
        }

        if ($isArray) {
            return $questions;
        }

        return $questions[0];
    }
}
