<?php

declare(strict_types=1);

namespace App\Service\Abstracts;

use App\Abstracts\ServiceAbstracts;
use App\Constant\ErrorConstant;
use App\Exception\ApiException;
use App\Exception\ValidationException;
use App\Helper\ArrayHelper;
use App\Helper\HtmlHelper;
use App\Helper\MarkdownHelper;
use App\Helper\ValidatorHelper;
use App\Traits\Base;
use App\Traits\Commentable;
use App\Traits\Followable;
use App\Traits\Votable;

/**
 * Article.php 和 Question.php 需要继承该类
 *
 * Class Post
 * @package App\Service\Abstracts
 */
abstract class Post extends ServiceAbstracts
{
    use Base, Commentable, Followable, Votable;

    /**
     * 返回主键字段名
     *
     * @return string
     */
    abstract protected function getPrimaryKey(): string;

    /**
     * 获取表名
     *
     * @return string
     */
    abstract protected function getTableName(): string;

    /**
     * 获取隐私字段
     *
     * @return array
     */
    public function getPrivacyFields(): array
    {
        return $this->container->roleService->managerId() ? [] : ['delete_time'];
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
        return [$this->getPrimaryKey(), 'user_id', 'topic_id']; // topic_id 需要另外写逻辑
    }

    /**
     * 获取文章列表
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

        // 根据 用户ID 获取文章列表
        if (isset($condition['user_id'])) {
            $this->container->userService->hasOrFail($condition['user_id']);

            $where['user_id'] = $condition['user_id'];
        }

        // 根据 话题ID 获取文章列表
        elseif (isset($condition['topic_id'])) {
            $this->container->topicService->hasOrFail($condition['topic_id']);

            $join = ['[><]topicable' => [$this->getPrimaryKey() => 'topicable_id']];
            $where['topicable.topic_id'] = $condition['topic_id'];
            $where['topicable.topicable_type'] = $this->getTableName();
        }

        // 根据 query 参数获取文章列表，参数包括 主键字段、user_id、topic_id
        else {
            $where = $this->getWhere();

            if (isset($where['topic_id'])) {
                $join = ['[><]topicable' => [$this->getPrimaryKey() => 'topicable_id']];
                $where['topicable.topic_id'] = $where['topic_id'];
                $where['topicable.topicable_type'] = $this->getTableName();
                unset($where['topic_id']);
            }

            if (isset($where['user_id'])) {
                $where[$this->getTableName() . '.user_id'] = $where['user_id'];
                unset($where['user_id']);
            }

            if (isset($where[$this->getPrimaryKey()])) {
                $where[$this->getTableName() . '.' . $this->getPrimaryKey()] = $where[$this->getPrimaryKey()];
                unset($where[$this->getPrimaryKey()]);
            }

            // 获取已删除的列表
            if (isset($condition['is_deleted']) && $condition['is_deleted']) {
                $this->currentModel->onlyTrashed();

                $defaultOrder = ['delete_time' => 'DESC'];
                $allowOrderFields = ArrayHelper::push($this->getAllowOrderFields(), 'delete_time');
                $order = $this->getOrder($defaultOrder, $allowOrderFields);
            }
        }

        $list = $this->currentModel
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
     * 发表文章
     *
     * @param  string $title           文章标题
     * @param  string $contentMarkdown Markdown 正文
     * @param  string $contentRendered HTML 正文
     * @param  array  $topicIds        话题ID数组
     * @return int                     主键值
     */
    public function create(
        string $title,
        string $contentMarkdown,
        string $contentRendered,
        array  $topicIds = null
    ): int {
        $userId = $this->container->roleService->userIdOrFail();

        [
            $title,
            $contentMarkdown,
            $contentRendered,
            $topicIds,
        ] = $this->createValidator(
            $title,
            $contentMarkdown,
            $contentRendered,
            $topicIds
        );

        // 添加文章
        $postId = (int)$this->currentModel->insert([
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
                'topicable_id'   => $postId,
                'topicable_type' => $this->getTableName(),
            ];
        }
        if ($topicable) {
            $this->container->topicableModel->insert($topicable);
        }

        // 自动关注该文章
        $this->addFollow($userId, $postId);

        // 用户的 post_count + 1
        $this->container->userModel
            ->where(['user_id' => $userId])
            ->update([$this->getTableName() . '_count[+]' => 1]);

        return $postId;
    }

    /**
     * 发表文章前对参数进行验证
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
    ): array {
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
     * 更新文章
     *
     * @param int    $postId
     * @param string $title
     * @param string $contentMarkdown
     * @param string $contentRendered
     * @param array  $topicIds         为 null 时表示不更新 topic_id
     */
    public function update(
        int    $postId,
        string $title = null,
        string $contentMarkdown = null,
        string $contentRendered = null,
        array  $topicIds = null
    ): void {
        [
            $data,
            $topicIds
        ] = $this->updateValidator(
            $postId,
            $title,
            $contentMarkdown,
            $contentRendered,
            $topicIds
        );

        // 更新文章信息
        if ($data) {
            $this->currentModel
                ->where([$this->getPrimaryKey() => $postId])
                ->update($data);
        }

        // 更新话题关系
        if (!is_null($topicIds)) {
            $this->container->topicableModel
                ->where([
                    'topicable_id'   => $postId,
                    'topicable_type' => $this->getTableName(),
                ])
                ->delete();

            $topicable = [];
            foreach ($topicIds as $topicId) {
                $topicable[] = [
                    'topic_id'       => $topicId,
                    'topicable_id'   => $postId,
                    'topicable_type' => $this->getTableName(),
                ];
            }

            if ($topicable) {
                $this->container->topicableModel->insert($topicable);
            }
        }
    }

    /**
     * 更新文章前的字段验证
     *
     * @param  int    $postId
     * @param  string $title
     * @param  string $contentMarkdown
     * @param  string $contentRendered
     * @param  array  $topicIds
     * @return array                   经过处理后的数据
     */
    private function updateValidator(
        int    $postId,
        string $title = null,
        string $contentMarkdown = null,
        string $contentRendered = null,
        array  $topicIds = null
    ): array {
        $data = [];

        $userId = $this->container->roleService->userIdOrFail();
        $postInfo = $this->currentModel->get($postId);

        if (!$postInfo) {
            throw new ApiException(ErrorConstant::ARTICLE_NOT_FOUND);
        }

        if ($postInfo['user_id'] != $userId && !$this->container->roleService->managerId()) {
            throw new ApiException(ErrorConstant::ARTICLE_ONLY_AUTHOR_CAN_EDIT);
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
     * 删除文章
     *
     * @param  int  $postId
     */
    public function delete(int $postId): void
    {
        $userId = $this->container->roleService->userIdOrFail();
        $postInfo = $this->currentModel->field('user_id')->get($postId);

        if (!$postInfo) {
            return;
        }

        if ($postInfo['user_id'] != $userId && !$this->container->roleService->managerId()) {
            throw new ApiException(ErrorConstant::ARTICLE_ONLY_AUTHOR_CAN_DELETE);
        }

        $this->currentModel->delete($postId);

        // 该文章的作者的 post_count - 1
        $this->container->userModel
            ->where(['user_id' => $postInfo['user_id']])
            ->update([$this->getTableName() . '_count[-]' => 1]);

        // 关注该文章的用户的 following_post_id - 1
        $followerIds = $this->container->followModel
            ->where(['followable_id' => $postId, 'followable_type' => $this->getTableName()])
            ->pluck('user_id');
        $this->container->userModel
            ->where(['user_id' => $followerIds])
            ->update(['following_' . $this->getTableName() . '_count[-]' => 1]);
    }

    /**
     * 批量软删除文章
     *
     * @param array $postIds
     */
    public function deleteMultiple(array $postIds): void
    {
        if (!$postIds) {
            return;
        }

        $posts = $this->currentModel
            ->field([$this->getPrimaryKey(), 'user_id'])
            ->select($postIds);

        if (!$posts) {
            return;
        }

        $postIds = array_column($posts, $this->getPrimaryKey());
        $this->currentModel->delete($postIds);

        $users = [];

        // 这些文章的作者的 post_count - 1
        foreach ($posts as $post) {
            isset($users[$post['user_id']][$this->getTableName() . '_count'])
                ? $users[$post['user_id']][$this->getTableName() . '_count'] += 1
                : $users[$post['user_id']][$this->getTableName() . '_count'] = 1;
        }

        // 关注这些文章的用户的 following_post_count - 1
        $followerIds = $this->container->followModel
            ->where(['followable_id' => $postIds, 'followable_type' => $this->getTableName()])
            ->pluck('user_id');

        foreach ($followerIds as $followerId) {
            isset($users[$followerId]['following_' . $this->getTableName() . '_count'])
                ? $users[$followerId]['following_' . $this->getTableName() . '_count'] += 1
                : $users[$followerId]['following_' . $this->getTableName() . '_count'] = 1;
        }

        foreach ($users as $userId => $user) {
            $update = [];

            if (isset($user[$this->getTableName() . '_count'])) {
                $update[$this->getTableName() . '_count[-]'] = $user[$this->getTableName() . '_count'];
            }

            if (isset($user['following_' . $this->getTableName() . '_count'])) {
                $update['following_' . $this->getTableName() . '_count[-]'] = $user['following_' . $this->getTableName() . '_count'];
            }

            $this->container->userModel
                ->where(['user_id' => $userId])
                ->update($update);
        }
    }

    /**
     * 恢复文章
     *
     * @param int $postId
     */
    public function restore(int $postId): void
    {

    }

    /**
     * 批量恢复文章
     *
     * @param array $postIds
     */
    public function restoreMultiple(array $postIds): void
    {

    }

    /**
     * 硬删除文章
     *
     * @param int $postId
     */
    public function destroy(int $postId): void
    {

    }

    /**
     * 批量硬删除文章
     *
     * @param array $postIds
     */
    public function destroyMultiple(array $postIds): void
    {

    }

    /**
     * 对数据库中取出的文章信息进行处理
     * todo 处理文章
     *
     * @param  array $posts 文章信息，或多个文章组成的数组
     * @return array
     */
    public function handle(array $posts): array
    {
        if (!$posts) {
            return $posts;
        }

        if (!$isArray = is_array(current($posts))) {
            $posts = [$posts];
        }

        foreach ($posts as &$post) {
        }

        if ($isArray) {
            return $posts;
        }

        return $posts[0];
    }

    /**
     * 获取在 relationship 中使用的 post
     *
     * @param  array $postIds
     * @return array
     */
    public function getInRelationship(array $postIds): array
    {
        $posts = array_combine($postIds, array_fill(0, count($postIds), []));

        $postsTmp = $this->currentModel
            ->field([$this->getPrimaryKey(), 'title', 'create_time', 'update_time'])
            ->select($postIds);

        foreach ($postsTmp as $item) {
            $posts[$item[$this->getPrimaryKey()]] = $item;
        }

        return $posts;
    }

    /**
     * 为文章添加 relationship 字段
     * {
     *     user: {}
     *     topics: [ {}, {}, {} ]
     *     is_following: false
     *     voting: up、down、''
     * }
     *
     * @param  array $posts
     * @param  array $relationship ['is_following': bool]
     * @return array
     */
    public function addRelationship(array $posts, array $relationship = []): array
    {
        if (!$posts) {
            return $posts;
        }

        if (!$isArray = is_array(current($posts))) {
            $posts = [$posts];
        }

        $postIds = array_unique(array_column($posts, $this->getPrimaryKey()));
        $userIds = array_unique(array_column($posts, 'user_id'));

        if (isset($relationship['is_following'])) {
            $followingArticleIds = $relationship['is_following'] ? $postIds : [];
        } else {
            $followingArticleIds = $this->container->followService->getIsFollowingInRelationship($postIds, $this->getTableName());
        }

        $votings = $this->container->voteService->getVotingInRelationship($postIds, $this->getTableName());
        $users = $this->container->userService->getInRelationship($userIds);
        $topics = $this->container->topicService->getInRelationship($postIds, $this->getTableName());

        // 合并数据
        foreach ($posts as &$post) {
            $post['relationship'] = [
                'user'         => $users[$post['user_id']],
                'topics'       => $topics[$post[$this->getPrimaryKey()]],
                'is_following' => in_array($post[$this->getPrimaryKey()], $followingArticleIds),
                'voting'       => $votings[$post[$this->getPrimaryKey()]],
            ];
        }

        if ($isArray) {
            return $posts;
        }

        return $posts[0];
    }
}
