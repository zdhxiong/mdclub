<?php

declare(strict_types=1);

namespace App\Model;

use App\Abstracts\ModelAbstracts;

/**
 * 举报模型
 */
class Report extends ModelAbstracts
{
    public $table = 'report';
    public $primaryKey = 'report_id';
    protected $timestamps = true;

    protected const UPDATE_TIME = false;

    public $columns = [
        'report_id',
        'reportable_id',
        'reportable_type',
        'user_id',
        'reason',
        'create_time',
    ];
}
