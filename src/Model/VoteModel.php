<?php

declare(strict_types=1);

namespace App\Model;

/**
 * Class VoteModel
 * @package App\Model
 */
class VoteModel extends Model
{
    protected $table = 'vote';
    protected $primaryKey = null;
    protected $timestamps = true;

    const UPDATE_TIME = false;

    public $columns = [
        'user_id',
        'votable_id',
        'votable_type',
        'type',
        'create_time',
    ];
}
