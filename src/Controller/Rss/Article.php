<?php

declare(strict_types=1);

namespace MDClub\Controller\Rss;

use MDClub\Constant\RouteNameConstant;
use MDClub\Facade\Service\ArticleService;
use MDClub\Facade\Transformer\ArticleTransformer;
use Psr\Http\Message\ResponseInterface;

/**
 * 文章 RSS
 */
class Article extends Abstracts
{
    /**
     * 文章列表 RSS
     *
     * @return ResponseInterface
     */
    public function getList(): ResponseInterface
    {
        ArticleTransformer::setInclude(['user']);

        $articles = ArticleService::getList();

        $articles['data'] = ArticleTransformer::transform($articles['data']);
        $title = "{$this->siteName} 的最新文章";
        $url = $this->url(RouteNameConstant::ARTICLES);
        $feedUrl = $this->url(RouteNameConstant::RSS_ARTICLES);
        $cacheKey = 'rss_articles';

        return $this->renderArticles($articles['data'], $title, $url, $feedUrl, $cacheKey);
    }
}
