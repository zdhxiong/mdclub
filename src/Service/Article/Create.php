<?php

declare(strict_types=1);

namespace MDClub\Service\Article;

use MDClub\Exception\ValidationException;

/**
 * 发布文章
 */
class Create extends Abstracts
{
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
}
