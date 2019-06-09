<?php

declare(strict_types=1);

namespace App\Traits;

use App\Constant\ErrorConstant;
use App\Exception\ApiException;
use Tightenco\Collect\Support\Collection;

/**
 * 可投票对象。用于 question, answer, article, comment
 *
 * @property-read \App\Abstracts\ModelAbstracts     $model
 * @property-read \App\Service\User\Get             $userGetService
 * @property-read \App\Service\Request              $requestService
 * @property-read \App\Model\Vote                   $voteModel
 * @property-read \App\Model\User                   $userModel
 */
trait Votable
{
    protected $isForApi = false;

    /**
     * @return Votable
     */
    public function forApi(): self
    {
        $this->isForApi = true;

        return $this;
    }

    /**
     * 添加投票
     *
     * @param  int    $userId
     * @param  int    $votableId
     * @param  string $type
     */
    public function add(int $userId, int $votableId, string $type): void
    {
        if (!in_array($type, ['up', 'down'])) {
            throw new ApiException(ErrorConstant::SYSTEM_VOTE_TYPE_ERROR);
        }

        $table = $this->model->table;

        $this->userGetService->hasOrFail($userId);
        $this->{"${table}GetService"}->hasOrFail($votableId);

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
                'create_time' => $this->requestService->time(),
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
     * @param  int $userId
     * @param  int $votableId
     */
    public function delete(int $userId, int $votableId): void
    {
        $this->userGetService->hasOrFail($userId);
        $this->{$this->model->table . 'GetService'}->hasOrFail($votableId);

        $table = $this->model->table;
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
    public function getCount(int $votableId): int
    {
        $votableTarget = $this->model->field('vote_count')->get($votableId);

        return $votableTarget['vote_count'];
    }

    /**
     * 获取指定对象的投票者
     *
     * @param  int              $votableId
     * @param  string           $type             投票类型：up, down，默认为获取所有类型
     * @return array|Collection
     */
    public function getVoters(int $votableId, string $type = null)
    {
        $table = $this->model->table;
        $this->{"${table}GetService"}->hasOrFail($votableId);

        if ($this->isForApi) {
            $this->userGetService->forApi();
            $this->isForApi = false;
        }

        $this->userGetService->beforeGet();

        if (in_array($type, ['up', 'down'])) {
            $this->userModel->where('vote.type', $type);
        }

        $result = $this->userModel
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

        $result = $this->userGetService->afterGet($result);

        return $this->userGetService->returnArray($result);
    }
}
