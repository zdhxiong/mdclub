<?php

declare(strict_types=1);

namespace MDClub\Service\Topic;

use MDClub\Helper\Request;
use MDClub\Service\Abstracts as ServiceAbstracts;
use MDClub\Traits\Url;
use Psr\Container\ContainerInterface;

/**
 * 话题抽象类
 */
abstract class Abstracts extends ServiceAbstracts
{
    use Url;

    /**
     * @var \MDClub\Model\Topic
     */
    protected $model;

    /**
     * @inheritDoc
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->model = $this->topicModel;
    }

    /**
     * 图片类型
     *
     * @return string
     */
    protected function getBrandType(): string
    {
        return 'topic-cover';
    }

    /**
     * 图片尺寸，宽高比为 0.56
     *
     * @return array
     */
    protected function getBrandSize(): array
    {
        return [
            's' => [360, 202],
            'm' => [720, 404],
            'l' => [1080, 606],
        ];
    }

    /**
     * 获取默认的封面图片地址
     *
     * @return array
     */
    protected function getDefaultBrandUrls(): array
    {
        $suffix = Request::isSupportWebp($this->request) ? 'webp' : 'jpg';
        $staticUrl = $this->getStaticUrl();
        $data['o'] = "{$staticUrl}default/topic_cover.{$suffix}";

        foreach (array_keys($this->getBrandSize()) as $size) {
            $data[$size] = "{$staticUrl}default/topic_cover_{$size}.{$suffix}";
        }

        return $data;
    }
}
