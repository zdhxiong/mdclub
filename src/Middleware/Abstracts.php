<?php

declare(strict_types=1);

namespace MDClub\Middleware;

use Psr\Container\ContainerInterface;

/**
 * 中间件抽象类
 *
 * @property-read \MDClub\Transformer\Answer       $answerTransformer
 * @property-read \MDClub\Transformer\Article      $articleTransformer
 * @property-read \MDClub\Transformer\Comment      $commentTransformer
 * @property-read \MDClub\Transformer\Follow       $followTransformer
 * @property-read \MDClub\Transformer\Image        $imageTransformer
 * @property-read \MDClub\Transformer\Question     $questionTransformer
 * @property-read \MDClub\Transformer\Report       $reportTransformer
 * @property-read \MDClub\Transformer\ReportDetail $reportDetailTransformer
 * @property-read \MDClub\Transformer\Topic        $topicTransformer
 * @property-read \MDClub\Transformer\User         $userTransformer
 * @property-read \MDClub\Transformer\Vote         $voteTransformer
 */
abstract class Abstracts
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __get(string $name)
    {
        return $this->container->get($name);
    }
}
