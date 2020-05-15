<?php

declare(strict_types=1);

namespace MDClub\Controller\Rss;

use MDClub\Constant\RouteNameConstant;
use MDClub\Facade\Service\ArticleService;
use MDClub\Facade\Transformer\ArticleTransformer;
use MDClub\Helper\Url;
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
        $this->setOrder();
        ArticleTransformer::setInclude(['user']);

        $articles = ArticleService::getList();

        $articles['data'] = ArticleTransformer::transform($articles['data']);
        $title = "{$this->siteName} 的最新文章";
        $url = Url::fromRoute(RouteNameConstant::ARTICLES);
        $feedUrl = Url::fromRoute(RouteNameConstant::RSS_ARTICLES);

        return $this->renderArticles($articles['data'], $title, $url, $feedUrl);
    }
}
