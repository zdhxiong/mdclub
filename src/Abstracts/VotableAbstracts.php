<?php

declare(strict_types=1);

namespace App\Abstracts;

use App\Helper\ArrayHelper;
use Psr\Container\ContainerInterface;
use App\Constant\ErrorConstant;
use App\Exception\ApiException;

/**
 * 投票抽象类
 *
 * Class VotableAbstracts
 * @package App\Abstracts
 */
abstract class VotableAbstracts extends ServiceAbstracts
{
    /**
     * 投票类型（question、answer、article、comment）
     *
     * @var string
     */
    protected $votableType;

    /**
     * 投票目标的 Model 实例
     */
    protected $votableTargetModel;

    /**
     * 投票目标的 Service 实例
     */
    protected $votableTargetService;

    /**
     * VotableAbstracts constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->votableTargetModel = $this->{$this->votableType . 'Model'};
        $this->votableTargetService = $this->{$this->votableType . 'Service'};
    }

    /**
     * 添加投票
     *
     * @param  int    $userId
     * @param  int    $votableId
     * @param  string $type
     */
    public function addVote(int $userId, int $votableId, string $type): void
    {
        if (!in_array($type, ['up', 'down'])) {
            throw new ApiException(ErrorConstant::SYSTEM_VOTE_TYPE_ERROR);
        }

        $this->userIdOrFail($userId);
        $this->votableIdOrFail($votableId);

        $voteWhere = [
            'user_id'      => $userId,
            'votable_id'   => $votableId,
            'votable_type' => $this->votableType,
        ];

        $vote = $this->voteModel->where($voteWhere)->get();

        if (!$vote) {
            // 没有投过票时，添加投票
            $this->voteModel->insert(array_merge($voteWhere, ['type' => $type]));

            $voteCount = [$type == 'up' ? '+' : '-', 1];
        } elseif ($type != $vote['type']) {
            // 新的投票与旧的投票不同时，修改原始投票
            $this->voteModel->where($voteWhere)->update([
                'type' => $type,
                'create_time' => $this->request->getServerParam('REQUEST_TIME'),
            ]);

            $voteCount = [$type == 'up' ? '+' : '-' , 2];
        }

        // 更新投票数量
        if (isset($voteCount)) {
            $this->votableTargetModel
                ->where([ $this->votableType . '_id' => $votableId ])
                ->update([ 'vote_count[' . $voteCount[0] . ']' => $voteCount[1] ]);
        }
    }

    /**
     * 删除投票
     *
     * @param  int $userId
     * @param  int $votableId
     */
    public function deleteVote(int $userId, int $votableId): void
    {
        $this->userIdOrFail($userId);
        $this->votableIdOrFail($votableId);

        $voteWhere = [
            'user_id'      => $userId,
            'votable_id'   => $votableId,
            'votable_type' => $this->votableType,
        ];

        $vote = $this->voteModel->where($voteWhere)->get();

        if ($vote) {
            $this->voteModel->where($voteWhere)->delete();

            $this->votableTargetModel
                ->where([ $this->votableType . '_id' => $votableId ])
                ->update([ 'vote_count[' . ($vote['type'] == 'up' ? '-' : '+') . ']' => 1 ]);
        }
    }

    /**
     * 获取投票总数（赞同票 - 反对票）
     *
     * @param int $votableId
     * @return int
     */
    public function getVoteCount(int $votableId): int
    {
        $votableTarget = $this->votableTargetModel->field(['vote_count'])->get($votableId);

        return $votableTarget['vote_count'];
    }

    /**
     * 获取指定对象的投票者
     *
     * @param  int    $votableId
     * @param  string $type             投票类型：up、down，默认为获取所有类型
     * @param  bool   $withRelationship
     * @return array
     */
    public function getVoters(int $votableId, string $type = null, bool $withRelationship = false): array
    {
        $this->votableIdOrFail($votableId);

        // 需要查询的字段
        $fields = ArrayHelper::remove(
            $this->userModel->columns,
            $this->userService->getPrivacyFields()
        );

        foreach ($fields as &$field) {
            $field = 'user.' . $field;
        }

        // 查询条件
        $where = [
            'vote.votable_id'   => $votableId,
            'vote.votable_type' => $this->votableType,
            'user.disable_time' => 0,
        ];

        if ($type == 'up') {
            $where['vote.type'] = 'up';
        } elseif ($type == 'down') {
            $where['vote.type'] = 'down';
        }

        $list = $this->userModel
            ->join([
                '[><]vote' => ['user_id' => 'user_id'],
            ])
            ->where($where)
            ->order([
                'vote.create_time' => 'DESC',
            ])
            ->field($fields)
            ->paginate();

        if ($withRelationship) {
            $list['data'] = $this->userService->addRelationship($list['data']);
        }

        return $list;
    }

    /**
     * 若用户不存在，则抛出异常
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
     * 若投票目标不存在，则抛出异常
     *
     * @param int $votableId
     */
    protected function votableIdOrFail(int $votableId): void
    {
        // 投票目标可能被软删除，但投票关系还在，所以需要查询投票目标表
        if (!$this->votableTargetService->has($votableId)) {
            $this->throwVotableNotFoundException();
        }
    }

    /**
     * 抛出投票目标不存在的异常
     *
     * @throws ApiException
     */
    protected function throwVotableNotFoundException(): void
    {
        $constants = [
            'question' => ErrorConstant::QUESTION_NOT_FOUND,
            'answer'   => ErrorConstant::ANSWER_NOT_FOUNT,
            'article'  => ErrorConstant::ARTICLE_NOT_FOUND,
            'comment'  => ErrorConstant::COMMENT_NOT_FOUNT,
        ];

        throw new ApiException($constants[$this->votableType]);
    }
}
