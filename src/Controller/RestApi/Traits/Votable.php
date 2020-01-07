<?php

declare(strict_types=1);

namespace MDClub\Controller\RestApi\Traits;

use MDClub\Facade\Library\Request;
use MDClub\Service\Interfaces\VotableInterface;

/**
 * 可投票。可用于 question, answer, article, comment
 */
trait Votable
{
    /**
     * @inheritDoc
     *
     * @return VotableInterface
     */
    abstract protected function getServiceInstance();

    /**
     * 获取投票者
     *
     * @param int $votableId
     *
     * @return array
     */
    public function getVoters(int $votableId): array
    {
        $service = $this->getServiceInstance();
        $type = Request::getQueryParams()['type'] ?? null;

        return $service->getVoters($votableId, $type);
    }

    /**
     * 添加投票
     *
     * @param int $votableId
     *
     * @return array
     */
    public function addVote(int $votableId): array
    {
        $service = $this->getServiceInstance();
        $type = Request::getParsedBody()['type'] ?? '';

        $service->addVote($votableId, $type);
        $voteCount = $service->getVoteCount($votableId);

        return ['vote_count' => $voteCount];
    }

    /**
     * 删除投票
     *
     * @param int $votableId
     *
     * @return array
     */
    public function deleteVote(int $votableId): array
    {
        $service = $this->getServiceInstance();

        $service->deleteVote($votableId);
        $voteCount = $service->getVoteCount($votableId);

        return ['vote_count' => $voteCount];
    }
}
