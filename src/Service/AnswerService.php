<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ServiceAbstracts;

/**
 * 对问题的回答
 *
 * Class AnswerService
 * @package App\Service
 */
class AnswerService extends ServiceAbstracts
{

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
