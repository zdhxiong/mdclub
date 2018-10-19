<?php

declare(strict_types=1);

namespace App\Abstracts;

use Psr\Container\ContainerInterface;
use App\Constant\ErrorConstant;
use App\Exception\ApiException;
use App\Helper\ArrayHelper;
use App\Service\Service;

/**
 * 关注关系抽象类
 *
 * Class FollowableAbstracts
 * @package App\Abstracts
 */
abstract class FollowableAbstracts extends Service
{
    /**
     * 关注类型（article、question、topic、user）
     *
     * @var string
     */
    protected $followableType;

    /**
     * 关注目标的 Model 实例
     */
    protected $followableTargetModel;

    /**
     * 关注目标的 Service 实例
     */
    protected $followableTargetService;

    /**
     * FollowableAbstracts constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->followableTargetModel = $this->{$this->followableType . 'Model'};
        $this->followableTargetService = $this->{$this->followableType . 'Service'};
    }

    /**
     * 获取指定对象的关注者列表，带分页
     *
     * @param  int    $followableId
     * @param  bool   $withRelationship
     * @return array
     */
    public function getFollowers(int $followableId, bool $withRelationship = false): array
    {
        $this->followableIdOrFail($followableId);

        $list = $this->followableModel
            ->where([
                'followable_id' => $followableId,
                'followable_type' => $this->followableType,
            ])
            ->field('user_id')
            ->order(['create_time' => 'DESC'])
            ->paginate();

        $followerIds = array_column($list['data'], 'user_id');
        $followers = $this->userService->getMultiple($followerIds);
        $list['data'] = ArrayHelper::merge($list['data'], $followers, 'user_id', 'user_id');

        if ($withRelationship) {
            if ($this->followableType == 'user') {
                $relationship = $followableId == $this->roleService->userId() ? ['is_followed' => true] : [];
            } else {
                $relationship = [];
            }

            $list['data'] = $this->userService->addRelationship($list['data'], $relationship);
        }

        return $list;
    }

    /**
     * 获取指定用户关注的对象列表，带分页
     *
     * @param  int   $userId
     * @param  bool  $withRelationship
     * @return array
     */
    public function getFollowing(int $userId, bool $withRelationship = false): array
    {
        $this->userIdOrFail($userId);

        $list = $this->followableModel
            ->where([
                'user_id' => $userId,
                'followable_type' => $this->followableType,
            ])
            ->field('followable_id')
            ->order(['create_time' => 'DESC'])
            ->paginate();

        $followingIds = array_column($list['data'], 'followable_id');
        $following = $this->followableTargetService->getMultiple($followingIds);
        $list['data'] = ArrayHelper::merge($list['data'], $following, 'followable_id', $this->followableType . '_id');

        if ($withRelationship) {
            $relationship = $userId == $this->roleService->userId() ? ['is_following' => true] : [];
            $list['data'] = $this->followableTargetService->addRelationship($list['data'], $relationship);
        }

        return $list;
    }

    /**
     * 检查 userId 是否关注了指定对象
     *
     * @param  int    $userId
     * @param  int    $followableId
     * @return bool
     */
    public function isFollowing(int $userId, int $followableId): bool
    {
        $this->userIdOrFail($userId);
        $this->followableIdOrFail($followableId);

        return $this->followableModel->where([
            'user_id'         => $userId,
            'followable_id'   => $followableId,
            'followable_type' => $this->followableType,
        ])->has();
    }

    /**
     * 添加关注
     *
     * @param  int    $userId
     * @param  int    $followableId
     * @return bool
     */
    public function addFollow(int $userId, int $followableId): bool
    {
        if ($this->isFollowing($userId, $followableId)) {
            $this->throwAlreadyFollowingException();
        }

        if ($this->followableType == 'user' && $userId == $followableId) {
            throw new ApiException(ErrorConstant::USER_CANT_FOLLOW_YOURSELF);
        }

        $this->followableModel->insert([
            'user_id'         => $userId,
            'followable_id'   => $followableId,
            'followable_type' => $this->followableType,
        ]);

        $this->userModel
            ->where(['user_id' => $userId])
            ->update(["following_{$this->followableType}_count[+]" => 1]);

        $this->followableTargetModel
            ->where(["{$this->followableType}_id" => $followableId])
            ->update(['follower_count[+]' => 1]);

        return true;
    }

    /**
     * 取消关注
     *
     * @param  int    $userId
     * @param  int    $followableId
     * @return bool
     */
    public function deleteFollow(int $userId, int $followableId): bool
    {
        if (!$this->isFollowing($userId, $followableId)) {
            $this->throwNotFollowingException();
        }

        $this->followableModel->where([
            'user_id'         => $userId,
            'followable_id'   => $followableId,
            'followable_type' => $this->followableType,
        ])->delete();

        $this->userModel
            ->where(['user_id' => $userId])
            ->update(["following_{$this->followableType}_count[-]" => 1]);

        $this->followableTargetModel
            ->where(["{$this->followableType}_id" => $followableId])
            ->update(['follower_count[-]' => 1]);

        return true;
    }

    /**
     * 若用户不存在，则抛出移除
     *
     * @param int $userId
     */
    protected function userIdOrFail(int $userId): void
    {
        if (!$this->userService->has($userId)) {
            throw new ApiException(ErrorConstant::USER_NOT_FOUND);
        }
    }

    /**
     * 若关注目标不存在，则抛出异常
     *
     * @param int $followableId
     */
    protected function followableIdOrFail(int $followableId): void
    {
        if (!$this->followableTargetService->has($followableId)) {
            $this->throwFollowableNotFoundException();
        }
    }

    /**
     * 抛出关注目标不存在异常
     */
    protected function throwFollowableNotFoundException(): void
    {
        $constants = [
            'article'  => ErrorConstant::ARTICLE_NOT_FOUND,
            'question' => ErrorConstant::QUESTION_NOT_FOUND,
            'topic'    => ErrorConstant::TOPIC_NOT_FOUND,
            'user'     => ErrorConstant::USER_TARGET_NOT_FOUNT,
        ];

        throw new ApiException($constants[$this->followableType]);
    }

    /**
     * 抛出已关注异常
     */
    protected function throwAlreadyFollowingException(): void
    {
        $constants = [
            'article'  => ErrorConstant::ARTICLE_ALREADY_FOLLOWING,
            'question' => ErrorConstant::QUESTION_ALREADY_FOLLOWING,
            'topic'    => ErrorConstant::TOPIC_ALREADY_FOLLOWING,
            'user'     => ErrorConstant::USER_ALREADY_FOLLOWING,
        ];

        throw new ApiException($constants[$this->followableType]);
    }

    /**
     * 抛出未关注异常
     */
    protected function throwNotFollowingException(): void
    {
        $constants = [
            'article'  => ErrorConstant::ARTICLE_NOT_FOLLOWING,
            'question' => ErrorConstant::QUESTION_NOT_FOLLOWING,
            'topic'    => ErrorConstant::TOPIC_NOT_FOLLOWING,
            'user'     => ErrorConstant::USER_NOT_FOLLOWING,
        ];

        throw new ApiException($constants[$this->followableType]);
    }
}
