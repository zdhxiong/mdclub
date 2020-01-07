<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Facade\Library\Auth;
use MDClub\Facade\Model\ArticleModel;
use MDClub\Facade\Model\CommentModel;
use MDClub\Facade\Model\FollowModel;
use MDClub\Facade\Model\ReportModel;
use MDClub\Facade\Model\TopicableModel;
use MDClub\Facade\Model\TopicModel;
use MDClub\Facade\Model\UserModel;
use MDClub\Facade\Model\VoteModel;
use MDClub\Facade\Service\ArticleService;
use MDClub\Facade\Service\CommentService;
use MDClub\Facade\Service\TopicService;
use MDClub\Facade\Service\UserService;
use MDClub\Facade\Validator\ArticleValidator;
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
 * 文章服务
 */
class Article extends Abstracts implements
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
        return \MDClub\Model\Article::class;
    }

    /**
     * 根据 user_id 获取文章列表
     *
     * @param int $userId
     *
     * @return array
     */
    public function getByUserId(int $userId): array
    {
        UserService::hasOrFail($userId);

        return ArticleModel::getByUserId($userId);
    }

    /**
     * 根据 topic_id 获取文章列表
     *
     * @param int $topicId
     *
     * @return array
     */
    public function getByTopicId(int $topicId): array
    {
        TopicService::hasOrFail($topicId);

        return ArticleModel::getByTopicId($topicId);
    }

    /**
     * 发表文章
     *
     * @param array $data [title, content_markdown, content_rendered, topic_id]
     *
     * @return int         主键值
     */
    public function create(array $data): int
    {
        $userId = Auth::userId();
        $createData = ArticleValidator::create($data);

        $articleId = (int)ArticleModel
            ::set('user_id', $userId)
            ->set('title', $createData['title'])
            ->set('content_markdown', $createData['content_markdown'])
            ->set('content_rendered', $createData['content_rendered'])
            ->insert();

        $this->updateTopicable($articleId, $createData['topic_id']);

        ArticleService::addFollow($articleId);
        UserModel::incArticleCount($userId);

        return $articleId;
    }

    /**
     * 更新文章
     *
     * @param int   $articleId
     * @param array $data [title, content_markdown, content_rendered, topic_id]
     */
    public function update(int $articleId, array $data): void
    {
        $updateData = ArticleValidator::update($articleId, $data);

        if (!$updateData) {
            return;
        }

        if (isset($updateData['title']) || isset($updateData['content_markdown'])) {
            ArticleModel::where('article_id', $articleId);

            if (isset($updateData['title'])) {
                ArticleModel::set('title', $updateData['title']);
            }

            if (isset($updateData['content_markdown'])) {
                ArticleModel
                    ::set('content_markdown', $updateData['content_markdown'])
                    ->set('content_rendered', $updateData['content_rendered']);
            }

            ArticleModel::update();
        }

        if (isset($updateData['topic_id'])) {
            $this->updateTopicable($articleId, $updateData['topic_id'], true);
        }
    }

    /**
     * 更新话题关系
     *
     * @param int   $articleId 文章ID
     * @param array $topicIds  话题ID数组
     * @param bool  $removeOld 是否要移除旧的话题关系
     */
    protected function updateTopicable(int $articleId, array $topicIds, bool $removeOld = false): void
    {
        // 移除旧的话题关系
        if ($removeOld) {
            $existTopicIds = TopicableModel
                ::where('topicable_type', 'article')
                ->where('topicable_id', $articleId)
                ->pluck('topic_id');

            $needDeleteTopicIds = array_diff($existTopicIds, $topicIds);
            if ($needDeleteTopicIds) {
                TopicableModel
                    ::where('topicable_type', 'article')
                    ->where('topicable_id', $articleId)
                    ->where('topic_id', $needDeleteTopicIds)
                    ->delete();
            }

            $topicIds = array_diff($topicIds, $existTopicIds);
        }

        // 添加新话题关系
        $topicable = [];
        foreach ($topicIds as $topicId) {
            $topicable[] = [
                'topic_id' => $topicId,
                'topicable_id' => $articleId,
                'topicable_type' => 'article',
            ];
        }
        if ($topicable) {
            TopicableModel::insert($topicable);
        }
    }

    /**
     * @inheritDoc
     */
    public function delete(int $articleId): void
    {
        $article = ArticleValidator::delete($articleId);

        if (!$article) {
            return;
        }

        $this->traitDelete($articleId, $article);
    }

    /**
     * 删除文章后的操作
     *
     * @param array $articles
     */
    public function afterDelete(array $articles): void
    {
        $articleIds = array_column($articles, 'article_id');

        // 作者的 article_count - 1, 关注者的 following_article_count - 1
        // [ user_id => [ article_count => count, following_article_count => count ] ]
        $users = [];

        foreach ($articles as $article) {
            $userId = $article['user_id'];

            isset($users[$userId]['article_count'])
                ? $users[$userId]['article_count'] += 1
                : $users[$userId]['article_count'] = 1;
        }

        $followerIds = FollowModel
            ::where('followable_type', 'article')
            ->where('followable_id', $articleIds)
            ->pluck('user_id');

        foreach ($followerIds as $followerId) {
            isset($users[$followerId]['following_article_count'])
                ? $users[$followerId]['following_article_count'] += 1
                : $users[$followerId]['following_article_count'] = 1;
        }

        foreach ($users as $userId => $user) {
            if (isset($user['article_count'])) {
                UserModel::dec('article_count', $user['article_count']);
            }

            if (isset($user['following_article_count'])) {
                UserModel::dec('following_article_count', $user['following_article_count']);
            }

            UserModel::where('user_id', $userId)->update();
        }

        // 文章所属的话题的 article_count - 1
        // [ topic_id => count ]
        $topics = [];
        $topicIds = TopicableModel
            ::where('topicable_type', 'article')
            ->where('topicable_id', $articleIds)
            ->pluck('topic_id');

        foreach ($topicIds as $topicId) {
            isset($topics[$topicId])
                ? $topics[$topicId] += 1
                : $topics[$topicId] = 1;
        }

        foreach ($topics as $topicId => $count) {
            TopicModel::decArticleCount($topicId, $count);
        }

        // 删除其他数据
        FollowModel::deleteByArticleIds($articleIds);
        ReportModel::deleteByArticleIds($articleIds);
        VoteModel::deleteByArticleIds($articleIds);
        TopicableModel::deleteByArticleIds($articleIds);

        $comments = CommentModel
            ::where('commentable_type', 'article')
            ->where('commentable_id', $articleIds)
            ->force()
            ->select();

        CommentModel::deleteByArticleIds($articleIds);
        CommentService::afterDelete($comments, true);
    }
}
