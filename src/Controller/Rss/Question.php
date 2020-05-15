<?php

declare(strict_types=1);

namespace MDClub\Controller\Rss;

use MDClub\Constant\RouteNameConstant;
use MDClub\Facade\Service\AnswerService;
use MDClub\Facade\Service\QuestionService;
use MDClub\Facade\Transformer\AnswerTransformer;
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
        $this->setOrder();
        QuestionTransformer::setInclude(['user']);

        $questions = QuestionService::getList();

        $questions['data'] = QuestionTransformer::transform($questions['data']);
        $title = "{$this->siteName} 的最新提问";
        $url = Url::fromRoute(RouteNameConstant::QUESTIONS);
        $feedUrl = Url::fromRoute(RouteNameConstant::RSS_QUESTIONS);

        return $this->renderQuestions($questions['data'], $title, $url, $feedUrl);
    }

    /**
     * 指定提问的回答列表的 RSS
     *
     * @param int $questionId
     *
     * @return ResponseInterface
     */
    public function getAnswers(int $questionId): ResponseInterface
    {
        $this->setOrder();
        AnswerTransformer::setInclude(['user']);

        $question = QuestionService::get($questionId);
        $answers = AnswerService::getByQuestionId($questionId);

        $answers['data'] = AnswerTransformer::transform($answers['data']);
        $title = "{$this->siteName} 中对提问“${question['title']}”的回答";
        $url = Url::fromRoute(RouteNameConstant::QUESTION, ['question_id' => $questionId]);
        $feedUrl = Url::fromRoute(RouteNameConstant::RSS_QUESTION_ANSWERS, ['question_id' => $questionId]);

        return $this->renderAnswers($answers['data'], $title, $url, $feedUrl);
    }
}
