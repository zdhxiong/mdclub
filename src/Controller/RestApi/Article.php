<?php

declare(strict_types=1);

namespace MDClub\Controller\RestApi;

use MDClub\Controller\RestApi\Traits\Commentable;
use MDClub\Controller\RestApi\Traits\Deletable;
use MDClub\Controller\RestApi\Traits\Followable;
use MDClub\Controller\RestApi\Traits\Getable;
use MDClub\Controller\RestApi\Traits\Votable;
use MDClub\Facade\Library\Request;
use MDClub\Facade\Service\ArticleService;

/**
 * 文章 API
 */
class Article extends Abstracts
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
        return \MDClub\Service\Article::class;
    }

    /**
     * 发表文章
     *
     * @return array
     */
    public function create(): array
    {
        $requestBody = Request::getParsedBody();
        $articleId = ArticleService::create($requestBody);

        return ArticleService::get($articleId);
    }

    /**
     * 更新文章
     *
     * @param int $articleId
     * @return array
     */
    public function update(int $articleId): array
    {
        $requestBody = Request::getParsedBody();
        ArticleService::update($articleId, $requestBody);

        return ArticleService::get($articleId);
    }
}
