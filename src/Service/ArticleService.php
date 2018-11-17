<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ServiceAbstracts;
use App\Traits\CommentableTraits;
use App\Traits\FollowableTraits;
use App\Traits\baseTraits;
use App\Traits\VotableTraits;

/**
 * 文章
 *
 * @property-read \App\Model\ArticleModel      currentModel
 *
 * Class ArticleService
 * @package App\Service
 */
class ArticleService extends ServiceAbstracts
{
    use baseTraits, CommentableTraits, FollowableTraits, VotableTraits;

    /**
     * 获取允许搜索的字段
     *
     * @return array
     */
    public function getAllowFilterFields(): array
    {
        return $this->roleService->managerId()
            ? [
                ''
            ]
            : [];
    }

    public function getList(bool $withRelationship = false): array
    {

    }

    public function create(): int
    {

    }

    private function createValidator(): array
    {
        return [];
    }

    /**
     * 删除文章
     *
     * @param  int  $articleId
     * @return bool
     */
    public function delete(int $articleId): bool
    {
        return true;
    }

    public function handle($data): array
    {
        return $data;
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
     * @param  array $articles
     * @param  array $relationship ['is_following': bool]
     * @return array
     */
    public function addRelationship(array $articles, array $relationship = []): array
    {
        if (!$articles) {
            return $articles;
        }

        if (!$isArray = is_array(current($articles))) {
            $articles = [$articles];
        }

        $articleIds = array_unique(array_column($articles, 'article_id'));
        $userIds = array_unique(array_column($articles, 'user_id'));

        // is_following
        if (isset($relationship['is_following'])) {
            $followingArticleIds = $relationship['is_following'] ? $articleIds : [];
        } else {
            $followingArticleIds = $this->followService->getIsFollowingInRelationship($articleIds, 'article');
        }

        $votings = $this->voteService->getVotingInRelationship($articleIds, 'article');
        $users = $this->userService->getUsersInRelationship($userIds);
        $topics = $this->topicService->getTopicsInRelationship($articleIds, 'article');

        // 合并数据
        foreach ($articles as &$article) {
            $article['relationship'] = [
                'user'         => $users[$article['user_id']],
                'topics'       => $topics[$article['article_id']],
                'is_following' => in_array($article['article_id'], $followingArticleIds),
                'voting'       => $votings[$article['article_id']],
            ];
        }

        if ($isArray) {
            return $articles;
        }

        return $articles[0];
    }
}
