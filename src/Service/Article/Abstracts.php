<?php /** @noinspection PhpFullyQualifiedNameUsageInspection */

declare(strict_types=1);

namespace MDClub\Service\Article;

use MDClub\Abstracts\ContainerProperty;
use Psr\Container\ContainerInterface;

/**
 * 文章抽象类
 */
abstract class Abstracts extends ContainerProperty
{
    /**
     * @var \MDClub\Model\Article
     */
    protected $model;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->model = $this->articleModel;
    }
}
