<?php

declare(strict_types=1);

namespace MDClub\Controller\Rss;

use MDClub\Constant\RouteNameConstant;
use MDClub\Facade\Service\ArticleService;
use MDClub\Facade\Service\QuestionService;
use MDClub\Facade\Service\TopicService;
use MDClub\Facade\Transformer\ArticleTransformer;
use MDClub\Facade\Transformer\QuestionTransformer;
use MDClub\Helper\Url;
use Psr\Http\Message\ResponseInterface;

/**
 * 话题 RSS
 */
class Topic extends Abstracts
{
    /**
     * 指定话题下的提问列表 RSS
     *
     * @param int $topicId
     *
     * @return ResponseInterface
     */
    public function getQuestions(int $topicId): ResponseInterface
    {
        QuestionTransformer::setInclude(['user']);

        $topic = TopicService::get($topicId);
        $questions = QuestionService::getByTopicId($topicId);

        $questions['data'] = QuestionTransformer::transform($questions['data']);
        $title = "{$this->siteName} 中 {$topic['name']} 话题下的提问";
        $url = Url::fromRoute(RouteNameConstant::QUESTION, [], ['topic_id' => $topicId]);
        $feedUrl = Url::fromRoute(RouteNameConstant::RSS_TOPIC_QUESTIONS, ['topic_id' => $topicId]);
        $cacheKey = "rss_topic_{$topicId}_questions";

        return $this->renderQuestions($questions['data'], $title, $url, $feedUrl, $cacheKey);
    }

    /**
     * 指定话题下的文章列表 RSS
     *
     * @param int $topicId
     *
     * @return ResponseInterface
     */
    public function getArticles(int $topicId): ResponseInterface
    {
        ArticleTransformer::setInclude(['user']);

        $topic = TopicService::get($topicId);
        $articles = ArticleService::getByTopicId($topicId);

        $articles['data'] = ArticleTransformer::transform($articles['data']);
        $title = "{$this->siteName} 中 {$topic['name']} 话题下的文章";
        $url = Url::fromRoute(RouteNameConstant::ARTICLE, [], ['topic_id' => $topicId]);
        $feedUrl = Url::fromRoute(RouteNameConstant::RSS_TOPIC_ARTICLES, ['topic_id' => $topicId]);
        $cacheKey = "rss_topic_{$topicId}_articles";

        return $this->renderArticles($articles['data'], $title, $url, $feedUrl, $cacheKey);
    }
}
