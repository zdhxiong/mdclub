<?php

declare(strict_types=1);

namespace MDClub\Controller\Rss;

use MDClub\Constant\RouteNameConstant;
use MDClub\Facade\Service\ArticleService;
use MDClub\Facade\Service\QuestionService;
use MDClub\Facade\Service\UserService;
use MDClub\Facade\Transformer\ArticleTransformer;
use MDClub\Facade\Transformer\QuestionTransformer;
use Psr\Http\Message\ResponseInterface;

/**
 * 用户 RSS
 */
class User extends Abstracts
{
    /**
     * 指定用户的提问列表 RSS
     *
     * @param int $userId
     *
     * @return ResponseInterface
     */
    public function getQuestions(int $userId): ResponseInterface
    {
        QuestionTransformer::setInclude(['user']);

        $user = UserService::get($userId);
        $questions = QuestionService::getByUserId($userId);

        $questions['data'] = QuestionTransformer::transform($questions['data']);
        $title = "{$this->siteName} 中 {$user['username']} 发表的提问";
        $url = $this->url(RouteNameConstant::QUESTIONS, [], ['user_id' => $userId]);
        $feedUrl = $this->url(RouteNameConstant::RSS_USER_QUESTIONS, ['user_id' => $userId]);
        $cacheKey = "rss_user_{$userId}_questions";

        return $this->renderQuestions($questions['data'], $title, $url, $feedUrl, $cacheKey);
    }

    /**
     * 指定用户的文章列表 RSS
     *
     * @param int $userId
     *
     * @return ResponseInterface
     */
    public function getArticles(int $userId): ResponseInterface
    {
        ArticleTransformer::setInclude(['user']);

        $user = UserService::get($userId);
        $articles = ArticleService::getByUserId($userId);

        $articles['data'] = ArticleTransformer::transform($articles['data']);
        $title = "{$this->siteName} 中 {$user['username']} 发表的文章";
        $url = $this->url(RouteNameConstant::ARTICLES, [], ['user_id' => $userId]);
        $feedUrl = $this->url(RouteNameConstant::RSS_USER_ARTICLES, ['user_id' => $userId]);
        $cacheKey = "rss_user_{$userId}_articles";

        return $this->renderArticles($articles['data'], $title, $url, $feedUrl, $cacheKey);
    }
}
