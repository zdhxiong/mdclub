<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\Abstracts\Post;

/**
 * 提问
 *
 * @property-read \App\Model\Question      currentModel
 *
 * Class Question
 * @package App\Service
 */
class Question extends Post
{
    protected function getPrimaryKey(): string
    {
        return 'question_id';
    }

    protected function getTableName(): string
    {
        return 'question';
    }
}
