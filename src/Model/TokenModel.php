<?php

declare(strict_types=1);

namespace App\Model;

/**
 * Class TokenModel
 *
 * @package App\Model
 */
class TokenModel extends Model
{
    protected $table = 'token';
    protected $primaryKey = 'token';
    protected $timestamps = true;

    const UPDATE_TIME = false;

    protected $columns = [
        'token',
        'user_id',
        'device',
        'create_time',
        'expire_time',
    ];
}
