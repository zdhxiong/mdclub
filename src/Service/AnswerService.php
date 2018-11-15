<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ServiceAbstracts;
use App\Constant\ErrorConstant;
use App\Exception\ApiException;
use App\Traits\CommentableTraits;
use App\Traits\VotableTraits;

/**
 * 对问题的回答
 *
 * @property-read \App\Model\AnswerModel      currentModel
 *
 * Class AnswerService
 * @package App\Service
 */
class AnswerService extends ServiceAbstracts
{
    use CommentableTraits, VotableTraits;

    /**
     * 判断指定回答是否存在
     *
     * @param  int  $answerId
     * @return bool
     */
    public function has(int $answerId): bool
    {
        return $this->answerModel->has($answerId);
    }

    /**
     * 若回答不存在，则抛出异常
     *
     * @param  int  $answerId
     * @return bool
     */
    public function hasOrFail(int $answerId): bool
    {
        if (!$isHas = $this->has($answerId)) {
            throw new ApiException(ErrorConstant::ANSWER_NOT_FOUNT);
        }

        return $isHas;
    }

    /**
     * 获取回答详情
     *
     * @param  int   $answerId
     * @param  bool  $withRelationship
     * @return array
     */
    public function get(int $answerId, bool $withRelationship = false): array
    {

    }

    /**
     * 获取多个回答信息
     *
     * @param  array $answerIds
     * @param  bool  $withRelationship
     * @return array
     */
    public function getMultiple(array $answerIds, bool $withRelationship = false): array
    {

    }
}
