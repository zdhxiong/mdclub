<?php

declare(strict_types=1);

namespace MDClub\Model;

use MDClub\Observer\Vote as VoteObserver;

/**
 * 投票模型
 */
class Vote extends Abstracts
{
    public $table = 'vote';
    protected $timestamps = true;
    protected $observe = VoteObserver::class;

    protected const UPDATE_TIME = false;

    public $columns = [
        'user_id',
        'votable_id',
        'votable_type',
        'type',
        'create_time',
    ];
}
