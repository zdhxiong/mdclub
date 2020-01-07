<?php

declare(strict_types=1);

namespace MDClub\Controller\RestApi;

use MDClub\Controller\RestApi\Traits\Deletable;
use MDClub\Controller\RestApi\Traits\Followable;
use MDClub\Controller\RestApi\Traits\Getable;
use MDClub\Facade\Library\Request;
use MDClub\Facade\Service\ArticleService;
use MDClub\Facade\Service\QuestionService;
use MDClub\Facade\Service\TopicService;

/**
 * 话题 API
 */
class Topic extends Abstracts
{
    use Followable;
    use Deletable;
    use Getable;

    /**
     * @inheritDoc
     */
    protected function getService(): string
    {
        return \MDClub\Service\Topic::class;
    }

    /**
     * 创建话题
     *
     * @return array
     */
    public function create(): array
    {
        $requestBody = Request::getParsedBody();
        $files = Request::getUploadedFiles();

        $topicId = TopicService::create(array_merge($requestBody, $files));

        return TopicService::get($topicId);
    }

    /**
     * 更新话题
     *
     * @param int $topicId
     *
     * @return array
     */
    public function update(int $topicId): array
    {
        $requestBody = Request::getParsedBody();
        $files = Request::getUploadedFiles();

        TopicService::update(
            $topicId,
            array_merge($requestBody, $files)
        );

        return TopicService::get($topicId);
    }

    /**
     * 根据话题ID获取提问列表
     *
     * @param int $topicId
     *
     * @return array
     */
    public function getQuestions(int $topicId): array
    {
        return QuestionService::getByTopicId($topicId);
    }

    /**
     * 根据话题ID获取文章列表
     *
     * @param int $topicId
     *
     * @return array
     */
    public function getArticles(int $topicId): array
    {
        return ArticleService::getByTopicId($topicId);
    }
}
