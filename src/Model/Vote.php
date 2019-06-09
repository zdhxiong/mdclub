<?php

declare(strict_types=1);

namespace App\Model;

use App\Abstracts\ModelAbstracts;

/**
 * 投票模型
 */
class Vote extends ModelAbstracts
{
    public $table = 'vote';
    protected $timestamps = true;

    protected const UPDATE_TIME = false;

    public $columns = [
        'user_id',
        'votable_id',
        'votable_type',
        'type',
        'create_time',
    ];
}
