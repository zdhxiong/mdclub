<?php

declare(strict_types=1);

namespace MDClub\Controller\RestApi\Traits;

use MDClub\Service\Interfaces\FollowableInterface;

/**
 * 可关注。用于 question, article, topic, user
 */
trait Followable
{
    /**
     * @inheritDoc
     *
     * @return FollowableInterface
     */
    abstract protected function getServiceInstance();

    /**
     * 获取关注者
     *
     * @param int $followableId
     *
     * @return array
     */
    public function getFollowers(int $followableId): array
    {
        $service = $this->getServiceInstance();

        return $service->getFollowers($followableId);
    }

    /**
     * 添加关注
     *
     * @param int $followableId
     *
     * @return array
     */
    public function addFollow(int $followableId): array
    {
        $service = $this->getServiceInstance();

        $service->addFollow($followableId);
        $followerCount = $service->getFollowerCount($followableId);

        return ['follower_count' => $followerCount];
    }

    /**
     * 取消关注
     *
     * @param int $followableId
     *
     * @return array
     */
    public function deleteFollow(int $followableId): array
    {
        $service = $this->getServiceInstance();

        $service->deleteFollow($followableId);
        $followerCount = $service->getFollowerCount($followableId);

        return ['follower_count' => $followerCount];
    }
}
