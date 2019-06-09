<?php

declare(strict_types=1);

namespace App\Model;

use App\Abstracts\ModelAbstracts;

/**
 * Token 模型
 */
class Token extends ModelAbstracts
{
    public $table = 'token';
    public $primaryKey = 'token';
    protected $timestamps = true;

    protected const UPDATE_TIME = false;

    public $columns = [
        'token',
        'user_id',
        'device',
        'create_time',
        'expire_time',
    ];
}
