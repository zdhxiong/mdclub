<?php

declare(strict_types=1);

namespace App\Service\Article;

use App\Constant\ErrorConstant;
use App\Exception\ApiException;
use App\Exception\ValidationException;
use App\Helper\HtmlHelper;
use App\Helper\MarkdownHelper;
use App\Helper\ValidatorHelper;

/**
 * 更新文章
 */
class Update extends Abstracts
{
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
        $userId = $this->roleService->userIdOrFail();

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
        $articleId = (int) $this->model->insert([
            'user_id'          => $userId,
            'title'            => $title,
            'content_markdown' => $contentMarkdown,
            'content_rendered' => $contentRendered,
        ]);

        // 添加话题关系
        $this->updateTopicable($articleId, $topicIds);

        // 自动关注该文章
        $this->articleFollowService->add($userId, $articleId);

        // 用户的 post_count + 1
        $this->userModel
            ->where('user_id', $userId)
            ->inc('article_count')
            ->update();

        return $articleId;
    }

    /**
     * 更新文章
     *
     * @param int    $articleId
     * @param string $title
     * @param string $contentMarkdown
     * @param string $contentRendered
     * @param array  $topicIds         为 null 时表示不更新 topic_id
     */
    public function update(
        int    $articleId,
        string $title = null,
        string $contentMarkdown = null,
        string $contentRendered = null,
        array  $topicIds = null
    ): void {
        [
            $data,
            $topicIds
        ] = $this->updateValidator(
            $articleId,
            $title,
            $contentMarkdown,
            $contentRendered,
            $topicIds
        );

        // 更新文章信息
        if ($data) {
            $this->model
                ->where('article_id', $articleId)
                ->update($data);
        }

        // 更新话题关系
        $this->updateTopicable($articleId, $topicIds, true);
    }

    /**
     * 更新话题关系
     *
     * @param int   $articleId  文章ID
     * @param array $topicIds   话题ID数组
     * @param bool  $checkExist 是否检查已存在的话题关系
     */
    protected function updateTopicable(int $articleId, array $topicIds, bool $checkExist = false): void
    {
        if ($checkExist) {
            $existTopicIds = $this->topicableModel
                ->where('topicable_type', 'article')
                ->where('topicable_id', $articleId)
                ->pluck('topic_id');

            $needDeleteTopicIds = array_diff($existTopicIds, $topicIds);
            if ($needDeleteTopicIds) {
                $this->topicableModel
                    ->where([
                        'topicable_type' => 'article',
                        'topicable_id' => $articleId,
                        'topic_id' => $needDeleteTopicIds,
                    ])->delete();
            }

            $topicIds = array_diff($topicIds, $existTopicIds);
        }

        $topicable = [];
        foreach ($topicIds as $topicId) {
            $topicable[] = [
                'topic_id'       => $topicId,
                'topicable_id'   => $articleId,
                'topicable_type' => 'article',
            ];
        }
        if ($topicable) {
            $this->topicableModel->insert($topicable);
        }
    }

    /**
     * 检查是否具有编辑权限
     *
     * @param int $articleId
     */
    protected function checkEditPermissions(int $articleId): void
    {
        if ($this->roleService->managerId()) {
            return;
        }

        $userId = $this->roleService->userIdOrFail();
        $article = $this->articleGetService->getOrFail($articleId);

        if ($article['user_id'] !== $userId) {
            throw new ApiException(ErrorConstant::ARTICLE_CANT_EDIT_NOT_AUTHOR);
        }

        $canEdit = $this->optionService->article_can_edit;
        $canEditBefore = $this->optionService->article_can_edit_before;
        $canEditOnlyNoComment = $this->optionService->article_can_edit_only_no_comment;
        $requestTime = $this->requestService->time();

        if (!$canEdit) {
            throw new ApiException(ErrorConstant::ARTICLE_CANT_EDIT);
        }

        if ($canEditBefore && $article['create_time'] + (int) $canEditBefore < $requestTime) {
            throw new ApiException(ErrorConstant::ARTICLE_CANT_EDIT_TIMEOUT);
        }

        if ($canEditOnlyNoComment && $article['comment_count']) {
            throw new ApiException(ErrorConstant::ARTICLE_CANT_EDIT_HAS_COMMENT);
        }
    }

    /**
     * 过滤不存在的 topic_id
     *
     * @param  array  $topicIds
     * @return array
     */
    protected function filterTopicIds(array $topicIds = null): array
    {
        if ($topicIds === null) {
            return [];
        }

        return $this->topicGetService
            ->fetchCollection()
            ->hasMultiple($topicIds)
            ->filter()
            ->keys()
            ->all();
    }

    /**
     * 验证标题，返回 errors 和处理后的 title
     *
     * @param  string $title
     * @return array         [errors, title]
     */
    protected function filterTitle(string $title): array
    {
        $errors = [];
        $title = strip_tags($title);

        if (!$title) {
            $errors['title'] = '标题不能为空';
        } elseif (!ValidatorHelper::isMin($title, 2)) {
            $errors['title'] = '标题长度不能小于 2 个字符';
        } elseif (!ValidatorHelper::isMax($title, 80)) {
            $errors['title'] = '标题长度不能超过 80 个字符';
        }

        return [$errors, $title];
    }

    /**
     * 验证内容，返回 errors 和处理后的 content
     *
     * @param  string $contentMarkdown
     * @param  string $contentRendered
     * @return array                    [errors, contentMarkdown, contentRendered]
     */
    protected function filterContent(string $contentMarkdown, string $contentRendered): array
    {
        $errors = [];

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
        if (!$errors && !ValidatorHelper::isMax(strip_tags($contentRendered), 100000)) {
            $errors['content_markdown'] = $errors['content_rendered'] = '正文不能超过 100000 个字';
        }

        return [$errors, $contentMarkdown, $contentRendered];
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
    protected function createValidator(
        string $title,
        string $contentMarkdown,
        string $contentRendered,
        array  $topicIds = null
    ): array {
        [$errors, $title] = $this->filterTitle($title);
        [$contentError, $contentMarkdown, $contentRendered] = $this->filterContent($contentMarkdown, $contentRendered);

        $errors = array_merge($errors, $contentError);

        if ($errors) {
            throw new ValidationException($errors);
        }

        $topicIds = $this->filterTopicIds($topicIds);

        return [$title, $contentMarkdown, $contentRendered, $topicIds];
    }

    /**
     * 更新文章前的字段验证
     *
     * @param  int    $articleId
     * @param  string $title
     * @param  string $contentMarkdown
     * @param  string $contentRendered
     * @param  array  $topicIds
     * @return array                   经过处理后的数据
     */
    protected function updateValidator(
        int    $articleId,
        string $title = null,
        string $contentMarkdown = null,
        string $contentRendered = null,
        array  $topicIds = null
    ): array {
        $this->checkEditPermissions($articleId);

        $errors = [];
        $data = [];

        // 验证标题
        if ($title !== null) {
            [$titleError, $title] = $this->filterTitle($title);

            if ($titleError) {
                $errors = array_merge($errors, $titleError);
            } else {
                $data['title'] = $title;
            }
        }

        // 验证正文
        if ($contentMarkdown !== null || $contentRendered !== null) {
            [$contentError, $contentMarkdown, $contentRendered] = $this->filterContent($contentMarkdown, $contentRendered);

            if ($contentError) {
                $errors = array_merge($errors, $contentError);
            } else {
                $data['content_markdown'] = $contentMarkdown;
                $data['content_rendered'] = $contentRendered;
            }
        }

        if ($errors) {
            throw new ValidationException($errors);
        }

        if ($topicIds !== null) {
            $topicIds = $this->filterTopicIds($topicIds);
        }

        return [$data, $topicIds];
    }
}
