<?php

declare(strict_types=1);

namespace App\Model;

/**
 * Class FollowModel
 *
 * @package App\Model
 */
class FollowModel extends Model
{
    protected $table = 'follow';
    protected $primaryKey = null;
    protected $timestamps = true;

    const UPDATE_TIME = false; // 不维护 update_time 字段

    public $columns = [
        'user_id',
        'followable_id',
        'followable_type',
        'create_time',
    ];
}
