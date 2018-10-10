<?php

declare(strict_types=1);

namespace App\Model;

/**
 * Class FollowableModel
 *
 * @package App\Model
 */
class FollowableModel extends Model
{
    protected $table = 'followable';
    protected $primaryKey = null;
    protected $timestamps = true;

    const UPDATE_TIME = false; // 不维护 update_time 字段

    protected $columns = [
        'user_id',
        'followable_id',
        'followable_type',
        'create_time',
    ];
}
