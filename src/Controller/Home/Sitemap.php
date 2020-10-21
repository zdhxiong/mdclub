<?php

declare(strict_types=1);

namespace MDClub\Controller\Home;

use MDClub\Constant\RouteNameConstant;
use MDClub\Facade\Library\Cache as CacheFacade;
use MDClub\Facade\Library\Request;
use MDClub\Facade\Model\ArticleModel;
use MDClub\Facade\Model\QuestionModel;
use MDClub\Facade\Model\TopicModel;
use MDClub\Helper\Url;
use MDClub\Initializer\App;
use Psr\Http\Message\ResponseInterface;

/**
 * 站点地图
 */
class Sitemap
{
    protected $time;
    protected $baseUrl;
    protected $folder = __DIR__ . '/../../../public/sitemap';
    protected $ttl = 3600 * 24;

    public function __construct()
    {
        $this->time = Request::time();
        $this->baseUrl = Url::hostPath() . '/sitemap/';
    }

    /**
     * 站点地图索引
     *
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $sitemapLastUpdateTime = CacheFacade::get('sitemap_last_update_time', 0);
        $indexPath = $this->folder . '/sitemap_index.xml';

        // 每 24 小时更新一次 sitemap
        if ($this->time - $sitemapLastUpdateTime > $this->ttl) {
            $index = new \samdark\sitemap\Index($indexPath);

            $sitemapUrls = array_merge(
                $this->generateQuestionSitemap(),
                $this->generateArticleSitemap(),
                $this->generateTopicSitemap()
            );

            foreach ($sitemapUrls as $sitemapUrl) {
                $index->addSitemap($sitemapUrl);
            }

            $index->write();

            CacheFacade::set('sitemap_last_update_time', $this->time, $this->ttl);
        }

        /** @var ResponseInterface $response */
        $response = App::$container->get(ResponseInterface::class);

        $response = $response->withHeader('Content-Type', 'application/xml; charset=utf-8');
        $response->getBody()->write(file_get_contents($indexPath));

        return $response;
    }

    /**
     * 根据最后更新时间获取更新频率
     *
     * @param int $lastModified
     *
     * @return string
     */
    protected function getChangeFrequency(int $lastModified): string
    {
        $timeDifference = $this->time - $lastModified;

        if ($timeDifference < 3600 * 3) {
            return \samdark\sitemap\Sitemap::HOURLY;
        }

        if ($timeDifference < 3600 * 10) {
            return \samdark\sitemap\Sitemap::DAILY;
        }

        if ($timeDifference < 3600 * 60) {
            return \samdark\sitemap\Sitemap::WEEKLY;
        }

        if ($timeDifference < 3600 * 1095) {
            return \samdark\sitemap\Sitemap::MONTHLY;
        }

        return \samdark\sitemap\Sitemap::YEARLY;
    }

    /**
     * 生成提问的 Sitemap 文件
     *
     * @return array
     */
    protected function generateQuestionSitemap(): array
    {
        $sitemap = new \samdark\sitemap\Sitemap($this->folder . '/questions.xml');
        $questions = QuestionModel::field('question_id, update_time')->select();

        foreach ($questions as $question) {
            $url = Url::fromRoute(RouteNameConstant::QUESTION, ['question_id' => $question['question_id']]);
            $lastModified = $question['update_time'];
            $changeFrequency = $this->getChangeFrequency($question['update_time']);
            $sitemap->addItem($url, $lastModified, $changeFrequency);
        }

        $sitemap->write();

        return $sitemap->getSitemapUrls($this->baseUrl);
    }

    /**
     * 生成文章的 Sitemap 文件
     *
     * @return array
     */
    protected function generateArticleSitemap(): array
    {
        $sitemap = new \samdark\sitemap\Sitemap($this->folder . '/articles.xml');
        $articles = ArticleModel::field('article_id, update_time')->select();

        foreach ($articles as $article) {
            $url = Url::fromRoute(RouteNameConstant::ARTICLE, ['article_id' => $article['article_id']]);
            $lastModified = $article['update_time'];
            $changeFrequency = $this->getChangeFrequency($article['update_time']);
            $sitemap->addItem($url, $lastModified, $changeFrequency);
        }

        $sitemap->write();

        return $sitemap->getSitemapUrls($this->baseUrl);
    }

    /**
     * 生成话题的 Sitemap 文件
     *
     * @return array
     */
    protected function generateTopicSitemap(): array
    {
        $sitemap = new \samdark\sitemap\Sitemap($this->folder . '/topics.xml');
        $topics = TopicModel::field('topic_id')->select();

        foreach ($topics as $topic) {
            $url = Url::fromRoute(RouteNameConstant::TOPIC, ['topic_id' => $topic['topic_id']]);
            $lastModified = null;
            $changeFrequency = \samdark\sitemap\Sitemap::WEEKLY;
            $sitemap->addItem($url, $lastModified, $changeFrequency);
        }

        $sitemap->write();

        return $sitemap->getSitemapUrls($this->baseUrl);
    }
}
