<?php

declare(strict_types=1);

namespace App\Model;

use App\Abstracts\ModelAbstracts;

/**
 * Class FollowModel
 *
 * @package App\Model
 */
class FollowModel extends ModelAbstracts
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
