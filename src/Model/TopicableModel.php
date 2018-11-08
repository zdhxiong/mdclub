<?php

declare(strict_types=1);

namespace App\Model;

class TopicableModel extends Model
{
    protected $table = 'topicable';
    protected $primaryKey = null;
    protected $timestamps = true;

    const UPDATE_TIME = false; // 不维护 update_time 字段

    public $columns = [
        'topic_id',
        'topicable_id',
        'topicable_type',
        'create_time',
    ];
}
