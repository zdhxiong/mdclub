<?php /** @noinspection PhpFullyQualifiedNameUsageInspection */

declare(strict_types=1);

namespace App\Service\Article;

use App\Abstracts\ContainerAbstracts;
use Psr\Container\ContainerInterface;

/**
 * 文章抽象类
 */
abstract class Abstracts extends ContainerAbstracts
{
    /**
     * @var \App\Model\Article
     */
    protected $model;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->model = $this->articleModel;
    }
}
