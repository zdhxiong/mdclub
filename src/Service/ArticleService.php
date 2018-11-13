<?php

declare(strict_types=1);

namespace App\Service;

use App\Constant\ErrorConstant;
use App\Exception\ApiException;

/**
 * 文章
 *
 * Class ArticleService
 * @package App\Service
 */
class ArticleService extends Service
{

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
     * 判断指定文章是否存在
     *
     * @param  int  $articleId
     * @return bool
     */
    public function has(int $articleId): bool
    {
        return $this->articleModel->has($articleId);
    }

    /**
     * 获取文章信息
     *
     * @param  int   $articleId
     * @param  bool  $withRelationship
     * @return array
     */
    public function get(int $articleId, bool $withRelationship = false): array
    {
        $articleInfo = $this->articleModel
            ->field($this->getPrivacyFields(), true)
            ->get($articleId);

        if (!$articleInfo) {
            throw new ApiException(ErrorConstant::ARTICLE_NOT_FOUND);
        }

        if ($withRelationship) {
            $articleInfo = $this->addRelationship($articleInfo);
        }

        return $articleInfo;
    }

    /**
     * 获取多个文章信息
     *
     * @param  array $articleIds
     * @param  bool  $withRelationship
     * @return array
     */
    public function getMultiple(array $articleIds, bool $withRelationship = false): array
    {
        if (!$articleIds) {
            return [];
        }

        $articles = $this->articleModel
            ->where(['article_id' => $articleIds])
            ->field($this->getPrivacyFields(), true)
            ->select();

        if ($withRelationship) {
            $articles = $this->addRelationship($articles);
        }

        return $articles;
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

    /**
     * 为文章添加 relationship 字段
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
     *     topics: [
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
     *     voting: up、down、false
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

        $currentUserId = $this->roleService->userId();
        $articleIds = array_unique(array_column($articles, 'article_id'));
        $userIds = array_unique(array_column($articles, 'user_id'));
        $followingArticleIds = [];
        $votings = []; // article_id 为键，投票类型为值
        $users = []; // user_id 为键，用户信息为值
        $topics = []; // article_id 为键，topic 信息组成的二维数组为值

        // is_following
        if ($currentUserId) {
            if (isset($relationship['is_following'])) {
                $followingArticleIds = $relationship['is_following'] ? $articleIds : [];
            } else {
                $followingArticleIds = $this->followModel->where([
                    'user_id'         => $currentUserId,
                    'followable_id'   => $articleIds,
                    'followable_type' => 'article',
                ])->pluck('followable_id');
            }
        }

        // voting
        if ($currentUserId) {
            $votes = $this->voteModel
                ->where([
                    'user_id'      => $currentUserId,
                    'votable_id'   => $articleIds,
                    'votable_type' => 'article',
                ])
                ->field(['votable_id', 'type'])
                ->select();

            foreach ($votes as $vote) {
                $votings[$vote['votable_id']] = $vote['type'];
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

        // topics
        $topicsTmp = $this->topicModel
            ->join([
                '[><]topicable' => ['topic_id' => 'topic_id']
            ])
            ->where([
                'topicable.topicable_id' => $articleIds,
                'topicable.topicable_type' => 'article',
            ])
            ->order(['topicable.create_time' => 'ASC'])
            ->field(['topic.topic_id', 'topic.name', 'topic.cover', 'topicable.topicable_id(article_id)'])
            ->select();
        foreach ($articleIds as $articleIdTmp) {
            $topics[$articleIdTmp] = [];
        }
        foreach ($topicsTmp as $item) {
            $topics[$item['article_id']][] = $this->topicService->handle([
                'topic_id' => $item['topic_id'],
                'name'     => $item['name'],
                'cover'    => $item['cover'],
            ]);
        }

        // 合并数据
        foreach ($articles as &$article) {
            $article['relationship'] = [
                'user'         => $users[$article['user_id']],
                'topics'       => $topics[$article['article_id']],
                'is_following' => in_array($article['article_id'], $followingArticleIds),
                'voting'       => $votings[$article['article_id']] ?? false,
            ];
        }

        if ($isArray) {
            return $articles;
        }

        return $articles[0];
    }
}
