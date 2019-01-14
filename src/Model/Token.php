<?php

declare(strict_types=1);

namespace App\Model;

use App\Abstracts\ModelAbstracts;

/**
 * Class Token
 * @package App\Model
 */
class Token extends ModelAbstracts
{
    public $table = 'token';
    public $primaryKey = 'token';
    protected $timestamps = true;

    const UPDATE_TIME = false;

    public $columns = [
        'token',
        'user_id',
        'device',
        'create_time',
        'expire_time',
    ];
}
