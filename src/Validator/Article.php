<?php

declare(strict_types=1);

namespace MDClub\Validator;

use MDClub\Constant\ApiErrorConstant;
use MDClub\Constant\OptionConstant;
use MDClub\Exception\ApiException;
use MDClub\Facade\Library\Auth;
use MDClub\Facade\Library\Option;
use MDClub\Facade\Library\Request;
use MDClub\Facade\Model\ArticleModel;
use MDClub\Facade\Service\ArticleService;
use MDClub\Validator\Traits\Markdown;

/**
 * 管理员可以编辑所有的文章，普通用户是否可编辑自己的文章由配置项设置
 *
 * 涉及的配置项：
 * article_can_edit                 是否可编辑（总开关）
 * article_can_edit_before          在发表后的多少秒内可编辑（0表示不作时间限制）
 * article_can_edit_only_no_comment 仅在没有评论时可编辑
 *
 *
 * 管理员可以删除所有的文章，普通用户是否可删除自己的文章由配置项设置
 *
 * 涉及的配置项：
 * article_can_delete                 是否可删除（总开关）
 * article_can_delete_before          在发表后的多少秒内可删除（0表示不作时间限制）
 * article_can_delete_only_no_comment 仅在没有评论时可删除
 */
class Article extends Abstracts
{
    use Markdown;

    protected $attributes = [
        'title' => '文章标题',
        'content_markdown' => '文章正文',
        'content_rendered' => '文章正文',
        'topic_ids' => '所属话题'
    ];

    /**
     * 创建时验证
     *
     * @param  array $data [title, content_markdown, content_rendered, topic_id]
     * @return array
     */
    public function create(array $data): array
    {
        return $this->data($data)
            ->field('title')->exist()->trim()->notEmpty()->length(2, 80)->htmlentities()
            ->field('content')->markdownExist()->markdownSupport(100000)
            ->field('topic_ids')->exist()->arrayUnique()->arrayLength(1, 10)->topicIdsExist()
            ->validate();
    }

    /**
     * 更新时验证
     *
     * @param  int   $articleId
     * @param  array $data      [title, content_markdown, content_rendered, topic_id]
     * @return array
     */
    public function update(int $articleId, array $data): array
    {
        $this->checkUpdatePermissions($articleId);

        return $this->data($data)
            ->field('title')->trim()->notEmpty()->length(2, 80)->htmlentities()
            ->field('content')->markdownSupport(100000)
            ->field('topic_ids')->arrayUnique()->arrayLength(1, 10)->topicIdsExist()
            ->validate();
    }

    /**
     * 删除前验证
     *
     * @param int $articleId
     *
     * @return array 文章信息
     */
    public function delete(int $articleId): array
    {
        return $this->checkDeletePermissions($articleId);
    }

    /**
     * 检查是否有权限进行更新
     *
     * 无权限时直接抛出异常，未抛出异常则表示有权限进行更新
     *
     * @param int $articleId
     */
    protected function checkUpdatePermissions(int $articleId): void
    {
        $article = ArticleService::getOrFail($articleId);

        if (Auth::isManager()) {
            return;
        }

        if ($article['user_id'] !== Auth::userId()) {
            throw new ApiException(ApiErrorConstant::ARTICLE_CANT_EDIT_NOT_AUTHOR);
        }

        $canEdit = Option::get(OptionConstant::ARTICLE_CAN_EDIT);
        $canEditBefore = (int) Option::get(OptionConstant::ARTICLE_CAN_EDIT_BEFORE);
        $canEditOnlyNoComment = Option::get(OptionConstant::ARTICLE_CAN_EDIT_ONLY_NO_COMMENT);

        if (!$canEdit) {
            throw new ApiException(ApiErrorConstant::ARTICLE_CANT_EDIT);
        }

        if ($canEditBefore && (int) $article['create_time'] + $canEditBefore < Request::time()) {
            throw new ApiException(ApiErrorConstant::ARTICLE_CANT_EDIT_TIMEOUT);
        }

        if ($canEditOnlyNoComment && $article['comment_count']) {
            throw new ApiException(ApiErrorConstant::ARTICLE_CANT_EDIT_HAS_COMMENT);
        }
    }

    /**
     * 检查是否有权限进行删除
     *
     * 无权限时直接抛出异常，未抛出异常则表示有权限进行删除
     *
     * 若资源不存在，也不会抛出异常
     *
     * @param int $articleId
     *
     * @return array 文章信息
     */
    protected function checkDeletePermissions(int $articleId): array
    {
        $article = ArticleModel::force()->get($articleId);

        if (!$article) {
            throw new ApiException(ApiErrorConstant::ARTICLE_NOT_FOUND);
        }

        if (Auth::isManager()) {
            return $article;
        } elseif ($article['delete_time']) {
            throw new ApiException(ApiErrorConstant::ARTICLE_NOT_FOUND);
        }

        if ($article['user_id'] !== Auth::userId()) {
            throw new ApiException(ApiErrorConstant::ARTICLE_CANT_DELETE_NOT_AUTHOR);
        }

        $canDelete = Option::get(OptionConstant::ARTICLE_CAN_DELETE);
        $canDeleteBefore = (int) Option::get(OptionConstant::ARTICLE_CAN_DELETE_BEFORE);
        $canDeleteOnlyNoComment = Option::get(OptionConstant::ARTICLE_CAN_DELETE_ONLY_NO_COMMENT);

        if (!$canDelete) {
            throw new ApiException(ApiErrorConstant::ARTICLE_CANT_DELETE);
        }

        if ($canDeleteBefore && (int) $article['create_time'] + $canDeleteBefore < Request::time()) {
            throw new ApiException(ApiErrorConstant::ARTICLE_CANT_DELETE_TIMEOUT);
        }

        if ($canDeleteOnlyNoComment && $article['comment_count']) {
            throw new ApiException(ApiErrorConstant::ARTICLE_CANT_DELETE_HAS_COMMENT);
        }

        return $article;
    }
}
