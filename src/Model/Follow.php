<?php

declare(strict_types=1);

namespace MDClub\Model;

use MDClub\Observer\Follow as FollowObserver;

/**
 * 关注模型
 */
class Follow extends Abstracts
{
    public $table = 'follow';
    protected $timestamps = true;
    protected $observe = FollowObserver::class;

    protected const UPDATE_TIME = false; // 不维护 update_time 字段

    public $columns = [
        'user_id',
        'followable_id',
        'followable_type',
        'create_time',
    ];
}
