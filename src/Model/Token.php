<?php

declare(strict_types=1);

namespace MDClub\Model;

/**
 * Token 模型
 */
class Token extends Abstracts
{
    public $table = 'token';
    public $primaryKey = 'token';
    protected $timestamps = true;

    public $columns = [
        'token',
        'user_id',
        'device',
        'create_time',
        'update_time',
        'expire_time',
    ];
}
