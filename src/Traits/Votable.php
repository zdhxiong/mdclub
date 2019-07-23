<?php

declare(strict_types=1);

namespace MDClub\Traits;

use MDClub\Constant\ApiError;
use MDClub\Exception\ApiException;
use MDClub\Helper\Request;
use Psr\Http\Message\ServerRequestInterface;

/**
 * 可投票对象。用于 question, answer, article, comment
 *
 * @property-read ServerRequestInterface  $request
 * @property-read \MDClub\Library\Auth    $auth
 * @property-read \MDClub\Model\Abstracts $model
 * @property-read \MDClub\Service\User    $userService
 * @property-read \MDClub\Model\Vote      $voteModel
 * @property-read \MDClub\Model\User      $userModel
 */
trait Votable
{
    /**
     * 添加投票
     *
     * @param  int    $votableId
     * @param  string $type
     */
    public function addVote(int $votableId, string $type): void
    {
        if (!in_array($type, ['up', 'down'])) {
            throw new ApiException(ApiError::SYSTEM_VOTE_TYPE_ERROR);
        }

        $table = $this->model->table;
        $userId = $this->auth->userId();

        $this->{"${table}Service"}->hasOrFail($votableId);

        $voteWhere = [
            'user_id'      => $userId,
            'votable_id'   => $votableId,
            'votable_type' => $table,
        ];

        $vote = $this->voteModel->where($voteWhere)->get();

        // 没有投过票时，添加投票
        if (!$vote) {
            $this->voteModel->insert(array_merge($voteWhere, ['type' => $type]));

            $type === 'up'
                ? $this->model->inc('vote_count')
                : $this->model->dec('vote_count');

            $this->model->where("${table}_id", $votableId)->update();
        }

        // 新的投票与旧的投票不同时，修改原始投票
        elseif ($type !== $vote['type']) {
            $this->voteModel->where($voteWhere)->update([
                'type' => $type,
                'create_time' => Request::time($this->request),
            ]);

            $type === 'up'
                ? $this->model->inc('vote_count', 2)
                : $this->model->dec('vote_count', 2);

            $this->model->where("${table}_id", $votableId)->update();
        }
    }

    /**
     * 删除投票
     *
     * @param  int $votableId
     */
    public function deleteVote(int $votableId): void
    {
        $userId = $this->auth->userId();
        $table = $this->model->table;

        $this->{"${table}Service"}->hasOrFail($votableId);

        $voteWhere = [
            'user_id'      => $userId,
            'votable_id'   => $votableId,
            'votable_type' => $table,
        ];

        $vote = $this->voteModel->where($voteWhere)->get();

        if ($vote) {
            $this->voteModel->where($voteWhere)->delete();

            $vote['type'] === 'up'
                ? $this->model->dec('vote_count')
                : $this->model->inc('vote_count');

            $this->model->where("${table}_id", $votableId)->update();
        }
    }

    /**
     * 获取投票总数（赞同票 - 反对票），可能出现负数
     *
     * @param int $votableId
     * @return int
     */
    public function getVoteCount(int $votableId): int
    {
        $votableTarget = $this->model->field('vote_count')->get($votableId);

        return $votableTarget['vote_count'] ?? 0;
    }

    /**
     * 获取指定对象的投票者
     *
     * @param  int    $votableId
     * @param  string $type      投票类型：up, down，默认为获取所有类型
     * @return array
     */
    public function getVoters(int $votableId, string $type = null): array
    {
        $table = $this->model->table;
        $this->{"${table}Service"}->hasOrFail($votableId);

        if (in_array($type, ['up', 'down'])) {
            $this->userModel->where('vote.type', $type);
        }

        return $this->userModel
            ->join([
                '[><]vote' => ['user_id' => 'user_id'],
            ])
            ->where([
                'vote.votable_id'   => $votableId,
                'vote.votable_type' => $table,
                'user.disable_time' => 0,
            ])
            ->order('vote.create_time', 'DESC')
            ->paginate();
    }
}
