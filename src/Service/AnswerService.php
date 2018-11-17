<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ServiceAbstracts;
use App\Traits\CommentableTraits;
use App\Traits\BaseTraits;
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
    use BaseTraits, CommentableTraits, VotableTraits;

    public function handle($data): array
    {
        return $data;
    }

    public function addRelationship(array $answers, array $relationship = []): array
    {

    }
}
