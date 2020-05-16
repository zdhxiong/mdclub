<?php

declare(strict_types=1);

namespace MDClub\Service\Interfaces;

/**
 * 可投票对象接口。用于 question, answer, article, comment
 */
interface VotableInterface
{
    /**
     * 添加投票
     *
     * @param  int    $votableId
     * @param  string $type
     */
    public function addVote(int $votableId, string $type): void;

    /**
     * 删除投票
     *
     * @param  int $votableId
     */
    public function deleteVote(int $votableId): void;

    /**
     * 获取投票数。包含 vote_count, vote_up_count, vote_down_count
     *
     * @param int $votableId
     * @return array
     */
    public function getVoteCount(int $votableId): array;

    /**
     * 获取指定对象的投票者
     *
     * @param  int    $votableId
     * @param  string $type      投票类型：up, down，默认为获取所有类型
     * @return array
     */
    public function getVoters(int $votableId, string $type = null): array;
}
