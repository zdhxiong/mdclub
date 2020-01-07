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
     * 获取投票总数（赞同票 - 反对票），可能出现负数
     *
     * @param int $votableId
     * @return int
     */
    public function getVoteCount(int $votableId): int;

    /**
     * 获取指定对象的投票者
     *
     * @param  int    $votableId
     * @param  string $type      投票类型：up, down，默认为获取所有类型
     * @return array
     */
    public function getVoters(int $votableId, string $type = null): array;
}
