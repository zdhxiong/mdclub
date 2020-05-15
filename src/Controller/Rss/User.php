<?php

declare(strict_types=1);

namespace MDClub\Controller\Rss;

use MDClub\Constant\RouteNameConstant;
use MDClub\Facade\Service\AnswerService;
use MDClub\Facade\Service\ArticleService;
use MDClub\Facade\Service\QuestionService;
use MDClub\Facade\Service\UserService;
use MDClub\Facade\Transformer\AnswerTransformer;
use MDClub\Facade\Transformer\ArticleTransformer;
use MDClub\Facade\Transformer\QuestionTransformer;
use MDClub\Helper\Url;
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
        $this->setOrder();
        QuestionTransformer::setInclude(['user']);

        $user = UserService::get($userId);
        $questions = QuestionService::getByUserId($userId);

        $questions['data'] = QuestionTransformer::transform($questions['data']);
        $title = "{$this->siteName} 中 {$user['username']} 发表的提问";
        $url = Url::fromRoute(RouteNameConstant::USER, ['user_id' => $userId]) . '#questions';
        $feedUrl = Url::fromRoute(RouteNameConstant::RSS_USER_QUESTIONS, ['user_id' => $userId]);

        return $this->renderQuestions($questions['data'], $title, $url, $feedUrl);
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
        $this->setOrder();
        ArticleTransformer::setInclude(['user']);

        $user = UserService::get($userId);
        $articles = ArticleService::getByUserId($userId);

        $articles['data'] = ArticleTransformer::transform($articles['data']);
        $title = "{$this->siteName} 中 {$user['username']} 发表的文章";
        $url = Url::fromRoute(RouteNameConstant::USER, ['user_id' => $userId]) . '#articles';
        $feedUrl = Url::fromRoute(RouteNameConstant::RSS_USER_ARTICLES, ['user_id' => $userId]);

        return $this->renderArticles($articles['data'], $title, $url, $feedUrl);
    }

    /**
     * 指定用户的回答列表 RSS
     *
     * @param int $userId
     *
     * @return ResponseInterface
     */
    public function getAnswers(int $userId): ResponseInterface
    {
        $this->setOrder();
        AnswerTransformer::setInclude(['user']);

        $user = UserService::get($userId);
        $answers = AnswerService::getByUserId($userId);

        $answers['data'] = AnswerTransformer::transform($answers['data']);
        $title = "{$this->siteName} 中用户“{$user['username']}”的回答";
        $url = Url::fromRoute(RouteNameConstant::USER, ['user_id' => $userId]) . '#answers';
        $feedUrl = Url::fromRoute(RouteNameConstant::RSS_USER_ANSWERS, ['user_id' => $userId]);

        return $this->renderAnswers($answers['data'], $title, $url, $feedUrl);
    }
}
