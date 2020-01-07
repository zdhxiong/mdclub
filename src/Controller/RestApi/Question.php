<?php

declare(strict_types=1);

namespace MDClub\Controller\RestApi;

use MDClub\Controller\RestApi\Traits\Commentable;
use MDClub\Controller\RestApi\Traits\Deletable;
use MDClub\Controller\RestApi\Traits\Followable;
use MDClub\Controller\RestApi\Traits\Getable;
use MDClub\Controller\RestApi\Traits\Votable;
use MDClub\Facade\Library\Request;
use MDClub\Facade\Service\AnswerService;
use MDClub\Facade\Service\QuestionService;

/**
 * 提问 API
 */
class Question extends Abstracts
{
    use Commentable;
    use Deletable;
    use Followable;
    use Getable;
    use Votable;

    /**
     * @inheritDoc
     */
    protected function getService(): string
    {
        return \MDClub\Service\Question::class;
    }

    /**
     * 创建提问
     *
     * @return array
     */
    public function create(): array
    {
        $requestBody = Request::getParsedBody();
        $questionId = QuestionService::create($requestBody);

        return QuestionService::get($questionId);
    }

    /**
     * 更新提问
     *
     * @param int $questionId
     * @return array
     */
    public function update(int $questionId): array
    {
        $requestBody = Request::getParsedBody();
        QuestionService::update($questionId, $requestBody);

        return QuestionService::get($questionId);
    }

    /**
     * 获取指定提问下的回答列表
     *
     * @param int $questionId
     * @return array
     */
    public function getAnswers(int $questionId): array
    {
        return AnswerService::getByQuestionId($questionId);
    }

    /**
     * 在指定提问下创建回答
     *
     * @param int $questionId
     * @return array
     */
    public function createAnswer(int $questionId): array
    {
        $requestBody = Request::getParsedBody();
        $answerId = AnswerService::create($questionId, $requestBody);

        return AnswerService::get($answerId);
    }
}
