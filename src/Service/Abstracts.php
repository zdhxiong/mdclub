<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Traits\ContainerProperty;
use Psr\Container\ContainerInterface;

/**
 * 服务抽象类
 *
 * @property-read \MDClub\Model\Answer       $answerModel
 * @property-read \MDClub\Model\Article      $articleModel
 * @property-read \MDClub\Model\Comment      $commentModel
 * @property-read \MDClub\Model\Follow       $followModel
 * @property-read \MDClub\Model\Image        $imageModel
 * @property-read \MDClub\Model\Inbox        $inboxModel
 * @property-read \MDClub\Model\Notification $notificationModel
 * @property-read \MDClub\Model\Question     $questionModel
 * @property-read \MDClub\Model\Report       $reportModel
 * @property-read \MDClub\Model\Token        $tokenModel
 * @property-read \MDClub\Model\Topic        $topicModel
 * @property-read \MDClub\Model\Topicable    $topicableModel
 * @property-read \MDClub\Model\User         $userModel
 * @property-read \MDClub\Model\Vote         $voteModel
 */
abstract class Abstracts
{
    use ContainerProperty;

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
