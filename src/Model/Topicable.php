<?php

declare(strict_types=1);

namespace App\Model;

use App\Abstracts\ModelAbstracts;

/**
 * 话题关系模型
 */
class Topicable extends ModelAbstracts
{
    public $table = 'topicable';
    protected $timestamps = true;

    protected const UPDATE_TIME = false; // 不维护 update_time 字段

    public $columns = [
        'topic_id',
        'topicable_id',
        'topicable_type',
        'create_time',
    ];
}
