<?php

declare(strict_types=1);

namespace MDClub\Controller;

use MDClub\Constant\ApiError;
use MDClub\Exception\ApiException;
use MDClub\Exception\SystemException;
use Psr\Http\Message\ResponseInterface;

/**
 * 文章
 */
class Article extends Abstracts
{
    /**
     * 文章列表页面
     *
     * @return ResponseInterface
     */
    public function pageIndex(): ResponseInterface
    {
        return $this->render('/article/index.php');
    }

    /**
     * 文章详情页面
     *
     * @param  int               $article_id
     * @return ResponseInterface
     */
    public function pageInfo(int $article_id): ResponseInterface
    {
        try {
            $article = $this->articleService->getOrFail($article_id);
        } catch (ApiException $e) {
            if ($e->getCode() === ApiError::ARTICLE_NOT_FOUND) {
                // todo  抛出 404
                throw new SystemException();
            } else {
                // todo 抛出500
                throw new SystemException();
            }
        }

        return $this->render('/article/info.php', ['article' => $article]);
    }
}
