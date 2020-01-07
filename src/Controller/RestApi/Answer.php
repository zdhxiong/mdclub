<?php

declare(strict_types=1);

namespace MDClub\Controller\RestApi;

use MDClub\Controller\RestApi\Traits\Commentable;
use MDClub\Controller\RestApi\Traits\Deletable;
use MDClub\Controller\RestApi\Traits\Getable;
use MDClub\Controller\RestApi\Traits\Votable;
use MDClub\Facade\Library\Request;
use MDClub\Facade\Service\AnswerService;

/**
 * 回答 API
 */
class Answer extends Abstracts
{
    use Commentable;
    use Deletable;
    use Getable;
    use Votable;

    /**
     * @inheritDoc
     */
    protected function getService(): string
    {
        return \MDClub\Service\Answer::class;
    }

    /**
     * 更新回答
     *
     * @param int $answerId
     *
     * @return array
     */
    public function update(int $answerId): array
    {
        $requestBody = Request::getParsedBody();
        AnswerService::update($answerId, $requestBody);

        return AnswerService::get($answerId);
    }
}
