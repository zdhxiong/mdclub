<?php

declare(strict_types=1);

namespace MDClub\Controller\Rss;

use MDClub\Constant\RouteNameConstant;
use MDClub\Facade\Service\QuestionService;
use MDClub\Facade\Transformer\QuestionTransformer;
use MDClub\Helper\Url;
use Psr\Http\Message\ResponseInterface;

/**
 * 提问 RSS
 */
class Question extends Abstracts
{
    /**
     * 提问列表 RSS
     *
     * @return ResponseInterface
     */
    public function getList(): ResponseInterface
    {
        QuestionTransformer::setInclude(['user']);

        $questions = QuestionService::getList();

        $questions['data'] = QuestionTransformer::transform($questions['data']);
        $title = "{$this->siteName} 的最新提问";
        $url = Url::fromRoute(RouteNameConstant::QUESTIONS);
        $feedUrl = Url::fromRoute(RouteNameConstant::RSS_QUESTIONS);
        $cacheKey = 'rss_questions';

        return $this->renderQuestions($questions['data'], $title, $url, $feedUrl, $cacheKey);
    }
}
