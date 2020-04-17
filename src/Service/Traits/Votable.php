<?php

declare(strict_types=1);

namespace MDClub\Service\Traits;

use MDClub\Constant\ApiErrorConstant;
use MDClub\Exception\ApiException;
use MDClub\Facade\Library\Auth;
use MDClub\Facade\Library\Request;
use MDClub\Facade\Model\UserModel;
use MDClub\Facade\Model\VoteModel;
use MDClub\Facade\Service\QuestionService;
use MDClub\Model\Abstracts as ModelAbstracts;

/**
 * 可投票对象。用于 question, answer, article, comment
 */
trait Votable
{
    /**
     * @inheritDoc
     */
    abstract public function getModelInstance(): ModelAbstracts;

    /**
     * 添加投票
     *
     * @param int    $votableId
     * @param string $type
     */
    public function addVote(int $votableId, string $type): void
    {
        if (!in_array($type, ['up', 'down'])) {
            throw new ApiException(ApiErrorConstant::COMMON_VOTE_TYPE_ERROR);
        }

        $model = $this->getModelInstance();
        $table = $model->table;

        /** @var QuestionService $class */
        $class = '\MDClub\Facade\Service\\' . ucfirst($table) . 'Service';

        $class::hasOrFail($votableId);

        $voteWhere = [
            'user_id' => Auth::userId(),
            'votable_id' => $votableId,
            'votable_type' => $table,
        ];

        $vote = VoteModel::where($voteWhere)->get();

        if (!$vote) {
            // 没有投过票时，添加投票
            VoteModel::insert(array_merge($voteWhere, ['type' => $type]));

            $type === 'up'
                ? $model->inc('vote_count')
                : $model->dec('vote_count');

            $model->where("${table}_id", $votableId)->update();
        } elseif ($type !== $vote['type']) {
            // 新的投票与旧的投票不同时，修改原始投票
            VoteModel
                ::where($voteWhere)
                ->set('type', $type)
                ->set('create_time', Request::time())
                ->update();

            $type === 'up'
                ? $model->inc('vote_count', 2)
                : $model->dec('vote_count', 2);

            $model->where("${table}_id", $votableId)->update();
        }
    }

    /**
     * 删除投票
     *
     * @param int $votableId
     */
    public function deleteVote(int $votableId): void
    {
        $model = $this->getModelInstance();
        $table = $model->table;

        /** @var QuestionService $class */
        $class = '\MDClub\Facade\Service\\' . ucfirst($table) . 'Service';

        $class::hasOrFail($votableId);

        $voteWhere = [
            'user_id' => Auth::userId(),
            'votable_id' => $votableId,
            'votable_type' => $table,
        ];

        $vote = VoteModel::where($voteWhere)->get();

        if ($vote) {
            VoteModel::where($voteWhere)->delete();

            $vote['type'] === 'up'
                ? $model->dec('vote_count')
                : $model->inc('vote_count');

            $model->where("${table}_id", $votableId)->update();
        }
    }

    /**
     * 获取投票总数（赞同票 - 反对票），可能出现负数
     *
     * @param int $votableId
     *
     * @return int
     */
    public function getVoteCount(int $votableId): int
    {
        $model = $this->getModelInstance();
        $votableTarget = $model->field('vote_count')->get($votableId);

        return $votableTarget['vote_count'] ?? 0;
    }

    /**
     * 获取指定对象的投票者
     *
     * @param int    $votableId
     * @param string $type 投票类型：up, down，默认为获取所有类型
     *
     * @return array
     */
    public function getVoters(int $votableId, string $type = null): array
    {
        $model = $this->getModelInstance();
        $table = $model->table;

        /** @var QuestionService $class */
        $class = '\MDClub\Facade\Service\\' . ucfirst($table) . 'Service';

        $class::hasOrFail($votableId);

        if (in_array($type, ['up', 'down'])) {
            UserModel::where('vote.type', $type);
        }

        return UserModel
            ::join(
                [
                    '[><]vote' => ['user_id' => 'user_id'],
                ]
            )
            ->where(
                [
                    'vote.votable_id' => $votableId,
                    'vote.votable_type' => $table,
                    'user.disable_time' => 0,
                ]
            )
            ->order('vote.create_time', 'DESC')
            ->paginate();
    }
}
